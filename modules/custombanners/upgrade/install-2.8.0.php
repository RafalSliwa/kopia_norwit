<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_8_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $all_banners_data = $module_obj->db->executeS('
        SELECT * FROM ' . _DB_PREFIX_ . 'custombanners
    ');
    $rows = [];
    foreach ($all_banners_data as $d) {
        $content = json_decode($d['content'], true);
        if (isset($content['restricted'])) {
            $type = $content['restricted']['type'];
            $ids = $content['restricted']['ids'];
            unset($content['restricted']);
            if ($ids) {
                $imploded_ids = implode(', ', $ids);
                $content['exceptions'] = [
                    'page' => ['type' => $type, 'ids' => $imploded_ids],
                    'customer' => ['type' => '0', 'ids' => ''],
                ];
                $content = json_encode($content);
                $row = '(' . (int) $d['id_banner'] . ', ' . (int) $d['id_shop'] . ', ' . (int) $d['id_lang'];
                $row .= ', \'' . pSQL($content, true) . '\')';
                $rows[] = $row;
            }
        }
    }
    if ($rows) {
        $module_obj->db->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'custombanners (id_banner, id_shop, id_lang, content)
            VALUES ' . implode(', ', $rows) . ' ON DUPLICATE KEY UPDATE content = VALUES(content)
        ');
    }

    return true;
}
