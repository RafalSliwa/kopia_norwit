<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_9_5($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // /custom/shop1.css|js -> /custom.css|js
    $id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
    $module_dir = _PS_MODULE_DIR_ . $module_obj->name;
    foreach ($module_obj->customCode('getTypes') as $type) {
        $saved_code_path = $module_dir . '/views/' . $type . '/custom/shop' . $id_shop_default . '.' . $type;
        $saved_code = file_exists($saved_code_path) ? Tools::file_get_contents($saved_code_path) : '';
        if ($type == 'css') {
            // move nav styles to $saved_code
            $demo_data_zip = $module_dir . '/defaults/data' . ($module_obj->is_16 ? '-16' : '') . '.zip';
            if ($tmp_dir = $module_obj->data()->extractZipToTemporaryDirectory($demo_data_zip)) {
                if (file_exists($tmp_dir . 'custom.css')) {
                    $new_css = Tools::file_get_contents($tmp_dir . 'custom.css');
                    $nav_styles_css = current(explode('/* home slider texts */', $new_css));
                    $saved_code = $nav_styles_css . $saved_code;
                }
                $module_obj->recursiveRemove($tmp_dir);
            }
        }
        if ($saved_code) {
            $module_obj->customCode('save', ['type' => $type, 'code' => $saved_code]);
        }
    }
    $module_obj->sliderLibrary('updateData', ['type' => 'bx', 'load' => 1]);
    $module_obj->updateAllWrappersSettings();

    return true;
}
