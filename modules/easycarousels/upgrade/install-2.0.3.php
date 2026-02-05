<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_0_3($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $all = $module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels');
    $rows = [];
    foreach ($all as $a) {
        $settings = json_decode($a['settings'], true);
        if (isset($settings['carousel']['activate_carousel'])) {
            $type = $settings['carousel']['activate_carousel'];
            unset($settings['carousel']['activate_carousel']);
        } else {
            $type = 1;
        }
        $settings['carousel']['type'] = $settings['carousel_m']['type'] = $type;
        $settings = json_encode($settings);
        $rows[] = '(' . (int) $a['id_carousel'] . ', ' . (int) $a['id_shop'] . ', \'' . pSQL($settings) . '\')';
    }
    $module_obj->db->execute('
        INSERT INTO ' . _DB_PREFIX_ . 'easycarousels (id_carousel, id_shop, settings)
        VALUES ' . implode(', ', $rows) . '
        ON DUPLICATE KEY UPDATE
        settings = VALUES(settings)
    ');

    return true;
}
