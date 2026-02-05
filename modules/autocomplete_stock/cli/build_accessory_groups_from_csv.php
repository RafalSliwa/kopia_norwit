#!/usr/bin/env php
<?php
/**
 * Generator accessory_groups.php z CSV
 *
 * CSV (UTF-8, separator ';') z nagłówkami:
 *   kategoria;id_produktu;nazwa_produktu
 *
 * Użycie:
 *   php build_accessory_groups_from_csv.php <wejście.csv> <wyjście.php> [--min=N]
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

if ($argc < 3) {
    fwrite(STDERR, "Użycie: php {$argv[0]} <wejście.csv> <wyjście.php> [--min=N]\n");
    exit(1);
}

$in  = $argv[1];
$out = $argv[2];
$min = 2;
for ($i = 3; $i < $argc; $i++) {
    if (preg_match('/^--min=(\d+)$/', $argv[$i], $m)) {
        $min = max(1, (int)$m[1]);
    }
}

if (!is_file($in)) {
    fwrite(STDERR, "Nie znaleziono pliku wejściowego: $in\n");
    exit(1);
}

/** Proste narzędzia stringowe */
function to_ascii(string $s): string {
    $map = [
        'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
        'Ą'=>'A','Ć'=>'C','Ę'=>'E','Ł'=>'L','Ń'=>'N','Ó'=>'O','Ś'=>'S','Ź'=>'Z','Ż'=>'Z',
    ];
    return strtr($s, $map);
}
if (!function_exists('mb_trim')) {
    function mb_trim(string $s): string {
        $r = preg_replace('/^\s+|\s+$/u', '', $s);
        return $r === null ? $s : $r;
    }
}
function tokens_ascii(string $s): array {
    $s = mb_strtolower(to_ascii($s), 'UTF-8');
    if (preg_match_all('/[0-9a-z]+/u', $s, $m)) return $m[0];
    return [];
}
/** Bardzo lekki „stemmer” PL na końcówki (ASCII) */
function pl_stem(string $w): string {
    $w = mb_strtolower(to_ascii($w), 'UTF-8');
    $ends = [
        'owie','owych','owego','owemu','owymi','owej','owym','owa','owe','owy',
        'ami','ach','owi','om','em','ie','ow','ów',
        'any','ane','ana','eni','ony','ona','one',
        'ym','ej','em',
        'a','e','i','y','o','u','ą','ę'
    ];
    foreach ($ends as $sfx) {
        $len = mb_strlen($sfx, 'UTF-8');
        if ($len >= 1 && mb_substr($w, -$len, null, 'UTF-8') === $sfx) {
            $base = mb_substr($w, 0, mb_strlen($w, 'UTF-8') - $len, 'UTF-8');
            if (mb_strlen($base, 'UTF-8') >= 4) return $base;
        }
    }
    return $w;
}

/** Stopwordy kategorii — ignorujemy przy wyborze rdzenia grupy */
$CAT_STOP = ['akcesoria','do','dla','i','oraz','|','/','-','—','–','the','of'];

/** Zliczacze: stem kategorii => head => count */
$counts = [];

/** Wczytanie CSV (semicolon, nagłówki) */
$f = new SplFileObject($in);
$f->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
$f->setCsvControl(';');

$header = null;
while (!$f->eof()) {
    $row = $f->fgetcsv();
    if ($row === false || $row === [null] || $row === null) continue;

    // Usuń BOM na pierwszej kolumnie (czasem Excel dodaje)
    if ($header === null) {
        if (!is_array($row)) continue;
        $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)($row[0] ?? ''));
        $header = array_map('mb_trim', $row);
        continue;
    }

    // zmapuj po nazwach
    $data = [];
    foreach ($row as $i => $val) {
        $key = $header[$i] ?? ('col'.$i);
        $data[$key] = is_string($val) ? mb_trim($val) : (string)$val;
    }

    $cat  = (string)($data['kategoria'] ?? '');
    $name = (string)($data['nazwa_produktu'] ?? '');
    if ($cat === '' || $name === '') continue;

    // --- WYBÓR RDZENIA GRUPY: preferuj token po "do"/"dla"
    $catTokens = tokens_ascii($cat);
    $groupStem = '';

    for ($i = 0; $i < count($catTokens); $i++) {
        if ($catTokens[$i] === 'do' || $catTokens[$i] === 'dla') {
            $next = $catTokens[$i+1] ?? '';
            if ($next !== '' && !in_array($next, $CAT_STOP, true)) {
                $stem = pl_stem($next);
                if ($stem !== '' && mb_strlen($stem, 'UTF-8') >= 4) {
                    $groupStem = $stem;
                }
            }
            break; // tylko pierwszy „do/dla”
        }
    }

    // fallback: jak nie było "do/dla", bierz pierwszy sensowny token
    if ($groupStem === '') {
        foreach ($catTokens as $t) {
            if (in_array($t, $CAT_STOP, true)) continue;
            if (mb_strlen($t, 'UTF-8') < 4) continue;
            $groupStem = pl_stem($t);
            if ($groupStem !== '') break;
        }
    }
    if ($groupStem === '') continue;

    // „głowa” akcesorium = pierwszy token z nazwy produktu (ASCII, lower)
    $nameTokens = tokens_ascii($name);
    if (empty($nameTokens)) continue;
    $head = $nameTokens[0];

    $counts[$groupStem][$head] = ($counts[$groupStem][$head] ?? 0) + 1;
}

/** Filtrowanie po progu --min */
$outMap = [];
foreach ($counts as $groupStem => $heads) {
    $kept = [];
    foreach ($heads as $head => $cnt) {
        if ($cnt >= $min) $kept[$head] = $cnt;
    }
    if (!empty($kept)) {
        // sortuj głowy po liczności DESC, potem alfabetycznie
        uksort($kept, function($a, $b) use ($kept) {
            $c = $kept[$b] <=> $kept[$a];
            return $c !== 0 ? $c : strcmp($a, $b);
        });
        $outMap[$groupStem] = array_values(array_keys($kept));
    }
}

/** Upewnij się, że katalog docelowy istnieje */
$dir = dirname($out);
if (!is_dir($dir)) {
    if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
        fwrite(STDERR, "Nie udało się utworzyć katalogu: $dir\n");
        exit(1);
    }
}

/** Zapis accessory_groups.php */
$php = "<?php\nreturn " . var_export($outMap, true) . ";\n";
if (file_put_contents($out, $php) === false) {
    fwrite(STDERR, "Nie udało się zapisać pliku: $out\n");
    exit(1);
}

echo "OK. Zapisano mapę do: $out\n";
echo "Grupy: " . count($outMap) . "\n";
