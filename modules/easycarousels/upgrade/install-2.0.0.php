<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_0_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $module_obj->prepareDatabaseTables();
    $module_obj->db->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'easycarousels`
        ADD `id_wrapper` INT(10) NOT NULL AFTER `hook_name`
    ');
    $module_obj->db->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'easycarousels`
        CHANGE `position` `position` INT(10) NOT NULL
    ');

    return true;
}
