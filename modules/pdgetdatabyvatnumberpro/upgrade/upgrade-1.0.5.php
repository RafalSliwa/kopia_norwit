<?php
/**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - Pd Get data by vat number Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   Pd Get data by vat number Pro for - PrestaShop 1.5.x and 1.6.x and 1.7.x
* @version   1.0.2
* @date      7-06-2018
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 1.1,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_1_0_5($module)
{
    /**
     * Do everything you want right there,
     * You could add a column in one of your module's tables
     */

    $module->registerHook('displayBackOfficeHeader');
    return true;
}
