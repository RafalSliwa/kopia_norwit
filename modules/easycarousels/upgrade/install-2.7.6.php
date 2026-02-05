<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_7_6($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // Media::clearCache(); // cleared in 2.7.7
    if ($module_obj->relatedOverrides()->isInstalled('classes/Product.php')) {
        $module_obj->relatedOverrides()->process('removeOverride', 'Product');
        $module_obj->relatedOverrides()->process('addOverride', 'Product');
    }

    return true;
}
