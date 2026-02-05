<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_2_7_3($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    // fill sales_days field in all carousels
    // $module_obj->normalizeAllCarouselSettings(); // normalized in 2.7.7
    // add column for caching
    $module_obj->db->execute('
        ALTER TABLE ' . _DB_PREFIX_ . 'ec_hook_settings
        ADD caching text NOT NULL AFTER display
    ');

    return true;
}
