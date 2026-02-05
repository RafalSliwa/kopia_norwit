<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_6_2($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // move code from front-16/17.css to custom.css
    $css_path = !$module_obj->is_16 ? '/views/css/front-17.css' : '/views/css/front-16.css';
    $overriden_css_path = _PS_THEME_DIR_ . (!$module_obj->is_16 ? '' : 'css/') . 'modules/' . $module_obj->name . $css_path;
    $source = file_exists($overriden_css_path) ? $overriden_css_path : _PS_MODULE_DIR_ . $module_obj->name . $css_path;
    if ($css = Tools::file_get_contents($source)) {
        // file_put_contents($module_obj->getCustomCodeFilePath('css'), $css);
        file_put_contents($module_obj->customCode('getFilePath', ['type' => 'css']), $css);
    }
    // fill missing settings: autoplay interval
    $required_fields = $module_obj->getRequiredFields();
    $all = $module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels');
    $rows = [];
    foreach ($all as $a) {
        $settings = json_decode($a['settings'], true);
        foreach ($required_fields as $key => $fields) {
            foreach ($fields as $name => $f) {
                if (!isset($settings[$key][$name])) {
                    $settings[$key][$name] = $f['value'];
                }
            }
        }
        $settings = json_encode($settings);
        $rows[] = '(' . (int) $a['id_carousel'] . ', ' . (int) $a['id_shop'] . ', \'' . pSQL($settings) . '\')';
    }
    if ($rows) {
        $module_obj->db->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'easycarousels (id_carousel, id_shop, settings)
            VALUES ' . implode(', ', $rows) . '
            ON DUPLICATE KEY UPDATE
            settings = VALUES(settings)
        ');
    }

    return true;
}
