#!/usr/bin/env php
<?php
/**
 * Generator irregulars.php z CSV produktów
 *
 * CSV: id_product;name
 *
 * Użycie:
 *   php build_irregulars_from_csv.php <wejście.csv> <wyjście.php> [--min=N]
 *
 * Wynik: tablica ['forma_mnoga' => 'forma_pojedyncza']
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

/** Helpers */
function to_ascii(string $s): string {
    $map = [
        'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
        'Ą'=>'A','Ć'=>'C','Ę'=>'E','Ł'=>'L','Ń'=>'N','Ó'=>'O','Ś'=>'S','Ź'=>'Z','Ż'=>'Z',
    ];
    return strtr($s, $map);
}
function tokens_ascii(string $s): array {
    $s = mb_strtolower(to_ascii($s), 'UTF-8');
    if (preg_match_all("/[0-9a-z]+/u", $s, $m)) return $m[0];
    return [];
}

/** Heurystyka: czy wygląda na liczbę mnogą */
function is_plural(string $w): bool {
    foreach (['y','i','ki','aki','eki','owie'] as $sfx) {
        if (str_ends_with($w, $sfx)) return true;
    }
    return false;
}

/** Prosta reguła: mnoga -> kandydat pojedynczej */
function singular_candidate(string $w): ?string {
    $map = [
        'y' => 'a',
        'i' => 'a',
        'ki' => 'ka',
        'aki' => 'ak',
        'eki' => 'ek',
        'owie' => '',
    ];
    foreach ($map as $sfx => $repl) {
        if (str_ends_with($w, $sfx)) {
            return substr($w, 0, -strlen($sfx)) . $repl;
        }
    }
    return null;
}

/** Zliczanie */
$pairs = [];
$f = new SplFileObject($in);
$f->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
$f->setCsvControl(';');

$header = null;
while (!$f->eof()) {
    $row = $f->fgetcsv();
    if ($row === [null] || $row === false) continue;

    if ($header === null) {
        $header = $row;
        continue;
    }

    $name = (string)($row[1] ?? '');
    $tokens = tokens_ascii($name);
    foreach ($tokens as $t) {
        if (mb_strlen($t) < 4) continue;
        if (is_plural($t)) {
            $sing = singular_candidate($t);
            if ($sing) {
                $pairs[$t][$sing] = ($pairs[$t][$sing] ?? 0) + 1;
            }
        }
    }
}

/** filtruj po min */
$outMap = [];
foreach ($pairs as $pl => $sings) {
    arsort($sings);
    $best = array_key_first($sings);
    if ($sings[$best] >= $min) {
        $outMap[$pl] = $best;
    }
}

/** zapis */
$php = "<?php\nreturn ".var_export($outMap, true).";\n";
if (file_put_contents($out, $php) === false) {
    fwrite(STDERR, "Nie udało się zapisać pliku: $out\n");
    exit(1);
}

echo "OK. Zapisano irregulars: $out\n";
echo "Par: ".count($outMap)."\n";
