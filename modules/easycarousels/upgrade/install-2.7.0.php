<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_7_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $module_obj->sliderLibrary('install');

    // add navigation styles to custom css
    $demo_path = _PS_MODULE_DIR_ . $module_obj->name . '/democontent/carousels-' . (!$module_obj->is_16 ? '17' : '16') . '.txt';
    if (file_exists($demo_path) && $demo_data = json_decode(Tools::file_get_contents($demo_path), true)) {
        $comment_after_nav_styles = "/*\n* The following styles";
        if (!empty($demo_data['css']) && Tools::strpos($demo_data['css'], $comment_after_nav_styles) !== false) {
            $updated_css = current(explode($comment_after_nav_styles, $demo_data['css']));
            $saved_custom_css = $module_obj->customCode('get', ['type' => 'css']);
            if (Tools::strpos($saved_custom_css, '/* carousel navigation */') === false) {
                $updated_css .= $saved_custom_css;
                $module_obj->customCode('save', ['code' => $updated_css, 'type' => 'css']);
            }
        }
    }

    // show navigation arrows on hover
    $all_carousels = $module_obj->db->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'easycarousels');
    $rows = [];
    foreach ($all_carousels as $c) {
        $settings = json_decode($c['settings'], true);
        if (!empty($settings['carousel']['n'])) {
            $settings['carousel']['n'] = 2; // display on hover
            $settings = json_encode($settings);
            $rows[] = '(' . (int) $c['id_carousel'] . ', ' . (int) $c['id_shop'] . ', \'' . pSQL($settings) . '\')';
        }
    }
    if ($rows) {
        $module_obj->db->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'easycarousels (id_carousel, id_shop, settings)
            VALUES ' . implode(', ', $rows) . ' ON DUPLICATE KEY UPDATE settings = VALUES(settings)
        ');
    }

    return true;
}
