<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

$xml = Tools::file_get_contents('ceneo.xml');

$array = json_decode(json_encode((array)simplexml_load_string($xml)),true);

echo '<pre>';
$simple = $xml;
$p = xml_parser_create();
xml_parse_into_struct($p, $simple, $vals, $index);
xml_parser_free($p);

$levels = [];

$total = 0;
$prev = 0;

$vals2 = [];

foreach ($vals as $a) {
    if ($a['tag'] != 'NAME') {
        continue;
    }

    $vals2[] = [
        'l' => $a['level'],
        'v' => $a['value'],
    ];
}

$limit = 1;

foreach ($vals2 as $i => $c) {
    $levels[$c['l']] = $c['v'];

    if (!empty($vals2[$i+1]) && ($vals2[$i+1]['l'] == $c['l'] || $vals2[$i+1]['l'] < $c['l'])) {
        $final = [];

        foreach ($levels as $l2 => $l) {
            if ($l2 <= $c['l']) {
                $final[] = $l;
            }
        }

        echo $limit .' - '. implode(' > ', $final);
        $limit++;
        echo '<br>';
    }
}

die('done');
