<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_9_8($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // Media::clearCache(); // called in /install-2.9.9
    $module_obj->db->execute('
        ALTER TABLE ' . _DB_PREFIX_ . 'custombanners
        ADD active_tablet tinyint(1) NOT NULL AFTER active,
        ADD active_mobile tinyint(1) NOT NULL AFTER active_tablet,
        ADD KEY active_tablet (active_tablet),
        ADD KEY active_mobile (active_mobile)
    ');
    $module_obj->db->execute('
        UPDATE ' . _DB_PREFIX_ . 'custombanners
        SET active_tablet = active, active_mobile = active
    ');

    return true;
}
