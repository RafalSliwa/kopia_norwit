<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_7_5($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $module_obj->unregisterHook('Header');
    $module_obj->unregisterHook('displayHeader');
    $module_obj->registerHook('actionFrontControllerSetMedia');
    // Product.php override is processed in 2.7.6
    // $module_obj->processOverride('removeOverride', $module_obj->getOverridePath('Product'), false);
    // $module_obj->processOverride('addOverride', $module_obj->getOverridePath('Product'), false);

    return true;
}
