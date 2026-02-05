<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_7_7($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    Media::clearCache();

    $module_obj->normalizeAllCarouselSettings();
    $module_obj->cachingSettings('adjustAll');
    $module_obj->cache('clear', '');

    $custom_css = $module_obj->customCode('get', ['type' => 'css']);
    $custom_css_upd = str_replace(
        ".easycarousels {\n\tmargin-bottom: 15px;\n}",
        ".easycarousels {\n\tmargin-bottom: 10px;\n}",
        $custom_css
    );
    if ($custom_css_upd != $custom_css) {
        $module_obj->customCode('save', ['type' => 'css', 'code' => $custom_css_upd]);
    }

    return true;
}
