#!/usr/bin/env php
<?php
/**
 * Generator synonyms.php z CSV produktów.
 *
 * Wejście CSV (UTF-8, separator ';', nagłówki): id_product;name
 *
 * Użycie:
 *   php cli/build_synonyms_from_csv.php <wejście.csv> <wyjście.php> [--min=N]
 *
 * --min=N   minimalna liczba wystąpień tokenu, aby dodać jego synonimy (domyślnie 1)
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

if ($argc < 3) {
    fwrite(STDERR, "Użycie: php {$argv[0]} <wejście.csv> <wyjście.php> [--min=N]\n");
    exit(1);
}
$in  = $argv[1];
$out = $argv[2];
$min = 1;
for ($i = 3; $i < $argc; $i++) {
    if (preg_match('/^--min=(\d+)$/', $argv[$i], $m)) {
        $min = max(1, (int)$m[1]);
    }
}

if (!is_file($in)) {
    fwrite(STDERR, "Nie znaleziono pliku wejściowego: $in\n");
    exit(1);
}

/** --- helpers --- */
function to_ascii(string $s): string {
    $map = [
        'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
        'Ą'=>'A','Ć'=>'C','Ę'=>'E','Ł'=>'L','Ń'=>'N','Ó'=>'O','Ś'=>'S','Ź'=>'Z','Ż'=>'Z',
    ];
    return strtr($s, $map);
}
function norm_token(string $s): string {
    $s = mb_strtolower($s, 'UTF-8');
    $s = to_ascii($s);
    $s = trim($s);
    return $s;
}
/** tokenizer: słowa + liczby + „/”, „-”, „.” i „,” wewnątrz bloków */
function tokenize_name(string $name): array {
    $name = (string)$name;
    $name = trim($name);
    if ($name === '') return [];
    // wyciągamy bloki z cyframi/literami i ewentualnie . , - / bez spacji
    if (preg_match_all("/[0-9A-Za-zÀ-žąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+(?:[.,\\/-][0-9A-Za-zÀ-žąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)*/u", $name, $m)) {
        return $m[0];
    }
    return [];
}

/** Z wyrazu typu "AX58" albo "P35A" budujemy warianty ze spacją: "AX 58", "P 35A". */
function alnum_space_variants(string $tok): array {
    $t = norm_token($tok);
    if ($t === '') return [];
    $out = [];

    // litery + cyfry + (opcjonalnie) litery
    if (preg_match('/^([a-z]+)(\d+)([a-z]+)?$/', $t, $m)) {
        $letters1 = $m[1];
        $digits   = $m[2];
        $letters2 = $m[3] ?? '';

        // podstawowy wariant: spacja między literami i cyframi
        $out[] = $letters1.' '.$digits.($letters2 !== '' ? $letters2 : '');
        // jeśli jest sufiks literowy – wariant z dodatkową spacją
        if ($letters2 !== '') {
            $out[] = $letters1.' '.$digits.' '.$letters2;
            $out[] = $letters1.$digits.' '.$letters2; // np. p35 a
            $out[] = $letters1.' '. $digits.$letters2; // np. p 35a
        }
    }
    // cyfry + litery
    if (preg_match('/^(\d+)([a-z]+)$/', $t, $m)) {
        $out[] = $m[1].' '.$m[2];
    }
    return array_values(array_unique($out));
}

/** Wymiary: kropka↔przecinek + spacja przed jednostką (m/cm/mm) */
function size_variants(string $tok): array {
    $t = norm_token($tok);
    if ($t === '') return [];
    $out = [];

    // złap formy 1,2m / 1.2m / 12mm / 12 cm itp.
    if (preg_match('/^(\d+(?:[.,]\d+)?)(\s*)(mm|cm|m)$/u', $t, $m)) {
        $num  = $m[1];
        $sp   = $m[2];
        $unit = $m[3];

        // spacja vs bez spacji
        $withSpace    = preg_replace('/\s*/', ' ', $num). ' ' . $unit;
        $withoutSpace = preg_replace('/\s*/', '', $num) . $unit;

        $out[] = $withSpace;
        $out[] = $withoutSpace;

        // kropka ↔ przecinek
        if (strpos($num, ',') !== false) {
            $numDot = str_replace(',', '.', $num);
            $out[] = $numDot . $unit;
            $out[] = $numDot . ' ' . $unit;
        } elseif (strpos($num, '.') !== false) {
            $numComma = str_replace('.', ',', $num);
            $out[] = $numComma . $unit;
            $out[] = $numComma . ' ' . $unit;
        }
    }

    // czyste liczby z jednostką sklejone wielkimi literami (np. 533MM) → znormalizuj i daj wariant ze spacją
    if (preg_match('/^(\d+)(mm|cm|m)$/i', $t, $m)) {
        $num  = $m[1];
        $unit = strtolower($m[2]);
        $out[] = $num.$unit;
        $out[] = $num.' '.$unit;
    }

    return array_values(array_unique($out));
}

/** Marki złożone (brand compounds): warianty z myślnikiem i bez spacji */
function brand_compound_variants(string $tok): array {
    $t = norm_token($tok);
    $out = [];
    // tylko dla 2-słownych (spacja w środku)
    if (strpos($t, ' ') !== false) {
        $compact = str_replace(' ', '', $t);
        $hyphen  = str_replace(' ', '-', $t);
        $out[] = $compact;
        $out[] = $hyphen;
    }
    return array_values(array_unique($out));
}

/** Z CSV budujemy mapę: token => [synonimy...] (dwukierunkowo) */
$f = new SplFileObject($in);
$f->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
$f->setCsvControl(';');

$header = null;
$freq   = []; // zliczenie wystąpień tokenów
$names  = [];

while (!$f->eof()) {
    $row = $f->fgetcsv();
    if ($row === [null] || $row === false) continue;

    if ($header === null) {
        $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)($row[0] ?? ''));
        $header = $row;
        continue;
    }

    // mapuj po nagłówku
    $data = [];
    foreach ($row as $i => $val) {
        $key = $header[$i] ?? ('col'.$i);
        $data[$key] = is_string($val) ? trim($val) : (string)$val;
    }
    $name = (string)($data['name'] ?? '');
    if ($name === '') continue;

    $names[] = $name;
    $toks = tokenize_name($name);
    foreach ($toks as $t) {
        $n = norm_token($t);
        if ($n === '') continue;
        $freq[$n] = ($freq[$n] ?? 0) + 1;
    }
}

// zbuduj synonymy
$map = []; // key => set(values)
$addPair = function(string $a, string $b) use (&$map) {
    $a = norm_token($a);
    $b = norm_token($b);
    if ($a === '' || $b === '' || $a === $b) return;
    if (!isset($map[$a])) $map[$a] = [];
    $map[$a][$b] = true;
};

foreach ($names as $name) {
    $toks = tokenize_name($name);

    // Zbuduj listę fraz 2-wyrazowych do brand_compound_variants (np. "wacker neuson", "altrad belle")
    $twoWordPhrases = [];
    for ($i = 0; $i+1 < count($toks); $i++) {
        $pair = norm_token($toks[$i]).' '.norm_token($toks[$i+1]);
        if (preg_match('/^[a-z]+ [a-z]+$/', $pair)) {
            $twoWordPhrases[] = $pair;
        }
    }

    foreach ($toks as $tokRaw) {
        $tok = norm_token($tokRaw);
        if ($tok === '') continue;
        if (($freq[$tok] ?? 0) < $min) continue;

        // 1) Warianty rozmiarów (przecinek/kropka, spacje jednostek)
        foreach (size_variants($tok) as $v) {
            if (($freq[$tok] ?? 0) >= $min) {
                $addPair($tok, $v);
                $addPair($v, $tok);
            }
        }

        // 2) Warianty alfanumeryczne (AX58 <-> AX 58, P35A <-> P 35A, GX160 <-> GX 160)
        foreach (alnum_space_variants($tok) as $v) {
            $addPair($tok, $v);
            $addPair($v, $tok);
        }
    }

    // 3) Marki złożone (np. "wacker neuson", "altrad belle")
    foreach (array_unique($twoWordPhrases) as $phrase) {
        foreach (brand_compound_variants($phrase) as $v) {
            $addPair($phrase, $v);
            $addPair($v, $phrase);
        }
    }
}

// posprzątaj do finalnej tablicy: array(key => [values...])
$final = [];
foreach ($map as $k => $set) {
    $vals = array_keys($set);
    sort($vals, SORT_NATURAL);
    $final[$k] = array_values($vals);
}
ksort($final, SORT_NATURAL);

// zapisz plik
$php = "<?php\nreturn " . var_export($final, true) . ";\n";
if (file_put_contents($out, $php) === false) {
    fwrite(STDERR, "Nie udało się zapisać pliku: $out\n");
    exit(1);
}

echo "OK. Zapisano synonyms do: $out\n";
echo "Wpisów: " . count($final) . "\n";
