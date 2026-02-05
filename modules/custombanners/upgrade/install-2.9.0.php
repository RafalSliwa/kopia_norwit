<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_9_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $module_obj->db->execute('
        ALTER TABLE ' . _DB_PREFIX_ . 'custombanners
        ADD publish_from datetime NOT NULL AFTER active,
        ADD publish_to datetime NOT NULL AFTER publish_from,
        ADD KEY publish_from (publish_from),
        ADD KEY publish_to (publish_to)
    ');
    $module_obj->updateAllWrappersSettings();

    return true;
}
