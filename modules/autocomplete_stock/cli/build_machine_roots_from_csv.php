#!/usr/bin/env php
<?php
/**
 * build_machine_roots_from_csv.php
 *
 * Wejście: CSV (UTF-8, ;) z nagłówkami:
 *   id_category;category_name;id_product;product_name
 *
 * Wyjście: machine_roots.php (PHP array) np.:
 *   return [
 *     'zacieraczka' => ['zacieraczki','zacieraczke','zacieraczek'],
 *     'zageszczarka' => ['zageszczarki','zageszczarke','zageszczarek'],
 *     'agregat' => ['agregaty','agregatu','agregacie'],
 *     ...
 *   ];
 *
 * Użycie:
 *   php cli/build_machine_roots_from_csv.php "cli/request_sql_8 (2).csv" modules/autocomplete_stock/machine_roots.php --min=5
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

if ($argc < 3) {
    fwrite(STDERR, "Użycie: php {$argv[0]} <wejście.csv> <wyjście.php> [--min=N]\n");
    exit(1);
}

$in  = $argv[1];
$out = $argv[2];
$min = 5;
for ($i=3; $i<$argc; $i++) {
    if (preg_match('/^--min=(\d+)$/', $argv[$i], $m)) $min = max(1, (int)$m[1]);
}

if (!is_file($in)) {
    fwrite(STDERR, "Nie znaleziono pliku wejściowego: $in\n");
    exit(1);
}

/* ===== utils ===== */
function to_ascii(string $s): string {
    return strtr($s, [
        'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ź'=>'z','ż'=>'z',
        'Ą'=>'A','Ć'=>'C','Ę'=>'E','Ł'=>'L','Ń'=>'N','Ó'=>'O','Ś'=>'S','Ź'=>'Z','Ż'=>'Z',
    ]);
}
function tokens(string $s): array {
    $s = mb_strtolower($s, 'UTF-8');
    // słowa (litery+cyfry), myślniki traktujemy jako separator
    if (preg_match_all('/[0-9\p{L}]+/u', $s, $m)) return $m[0];
    return [];
}
if (!function_exists('mb_trim')) {
    function mb_trim(string $s): string {
        $r = preg_replace('/^\s+|\s+$/u', '', $s);
        return $r === null ? $s : $r;
    }
}

/** lekka normalizacja i „stem” singularyzujący dla rzeczowników PL (bardzo uproszczony) */
function pl_singular_guess(string $w): string {
    $w = mb_strtolower($w, 'UTF-8');
    $w = to_ascii($w);
    $rules = [
        ['owie','a'],['ami','a'],['ach','a'],['owi','a'],['om','a'],['em','a'],['ów',''],
        ['ki','ka'], // zacieraczki -> zacieraczka
        ['y','a'], ['i','a'], ['e','a'], // agregaty -> agregata (potem korekta spółgł.+a -> bez 'a')
    ];
    foreach ($rules as [$from,$to]) {
        $len = mb_strlen($from,'UTF-8');
        if ($len>0 && mb_substr($w,-$len,null,'UTF-8') === $from) {
            $base = mb_substr($w,0,mb_strlen($w,'UTF-8')-$len,'UTF-8').$to;
            if (mb_strlen($base,'UTF-8')>=3) $w = $base;
            break;
        }
    }
    // kosmetyka: agregata -> agregat, młota -> młot, zestawa -> zestaw (spółgłoska + 'a' na końcu)
    if (preg_match('/[bcćdfghjklłmnprstwyzźż]a$/u', $w)) {
        $w = mb_substr($w, 0, -1, 'UTF-8');
    }
    return $w;
}

/** generuj kilka prostych form fleksyjnych (na potrzeby dopasowania zapytań) */
function pl_variants_simple(string $sing): array {
    $s = to_ascii(mb_strtolower($sing,'UTF-8'));
    $out = [];
    // liczba mnoga: -y / -i / -e (heurystyka)
    $out[] = $s.'y';
    $out[] = $s.'i';
    $out[] = $s.'e';
    // biernik (np. zacieraczkę -> ascii: zacieraczke)
    if (preg_match('/ka$/', $s)) $out[] = substr($s,0,-1).'e'; // -ka -> -ke
    else                          $out[] = $s.'e';
    // dopełniacz l.mn.: -ek (np. zageszczarek)
    if (!preg_match('/k$/',$s)) $out[] = $s.'ek';

    // unikalnie, bez pustych
    $out = array_values(array_unique(array_filter($out)));
    return $out;
}

/** Head’y, które traktujemy jako akcesoria – nie rdzenie maszyn (ASCII) */
$ACCESSORY_HEADS = array_map('to_ascii', [
    'talerz','dysk','płyta','płyty','płytka','noże','łopatki','filtr','zestaw','uchwyt','osprzęt','kółka',
    'wąż','węże','rura','adapter','rama','pokrowiec','olej','smar','bateria','zbiornik','ssawa','szczotka',
    'płyta','płyty','płyta','plyta','plyty', // redundancje ok; i tak ascii
]);

/* ===== zliczanie headów ===== */
$counts = []; // head_singular_ascii => count
$total  = 0;

$f = new SplFileObject($in);
$f->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD);
// USTAW: separator ;, cudzysłów ", escape \
$f->setCsvControl(';', '"', '\\');

$header = null;
while (!$f->eof()) {
    $row = $f->fgetcsv(); // bez parametrów – używa setCsvControl
    if ($row === false || $row === [null]) continue;

    if ($header === null) {
        // usuń BOM z 1. kolumny
        if (isset($row[0])) $row[0] = preg_replace('/^\xEF\xBB\xBF/u', '', (string)$row[0]);
        $header = array_map('mb_trim', $row);
        continue;
    }

    // zmapuj po nagłówkach
    $data = [];
    foreach ($row as $i=>$val) {
        $key = $header[$i] ?? ('col'.$i);
        $data[$key] = is_string($val) ? mb_trim($val) : (string)$val;
    }

    $name = (string)($data['product_name'] ?? $data['name'] ?? '');
    if ($name === '') continue;

    $toks = tokens($name);
    if (empty($toks)) continue;

    // wybierz pierwszy sensowny „head” nie będący akcesorium
    $head = null;
    foreach ($toks as $t) {
        $ta = to_ascii(mb_strtolower($t,'UTF-8'));
        if (mb_strlen($ta,'UTF-8') < 3) continue;
        if (in_array($ta, $ACCESSORY_HEADS, true)) continue;
        $head = $ta;
        break;
    }
    if ($head === null) continue;

    $sing = pl_singular_guess($head);
    if (in_array($sing, $ACCESSORY_HEADS, true)) continue;

    $counts[$sing] = ($counts[$sing] ?? 0) + 1;
    $total++;
}

/* ===== wybór rdzeni maszyn po progu --min ===== */
arsort($counts);
$roots = [];
foreach ($counts as $sing => $cnt) {
    if ($cnt < $min) continue;
    // odfiltruj ultra-ogólniki (ASCII)
    if (in_array($sing, [
        'zestaw','adapter','rama','pokrowiec','olej','smar','filtr','waz','rura','ssawa','szczotka','plyta','talerz','dysk','lopatki','bateria','zbiornik','uchwyt'
    ], true)) {
        continue;
    }
    $roots[$sing] = pl_variants_simple($sing);
}

/* ===== zapis ===== */
$php = "<?php\nreturn " . var_export($roots, true) . ";\n";

$dir = dirname($out);
if (!is_dir($dir)) {
    fwrite(STDERR, "Katalog wyjściowy nie istnieje: $dir\n");
    exit(1);
}
if (file_put_contents($out, $php) === false) {
    fwrite(STDERR, "Nie udało się zapisać pliku: $out\n");
    exit(1);
}

echo "OK. Zapisano machine_roots (".count($roots)." rdzeni) do: $out\n";
echo "Przetworzono wpisów: $total, unique_heads: " . count($counts) . "\n";
