<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    ING Lease Now
 * @copyright 2022-now ING Lease Now
 * @license   GNU General Public License
 **/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'
    . _DB_PREFIX_
    . 'leasenow` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_order` '
    . 'int(10) UNSIGNED NOT NULL, `id_leasing` varchar(255) NOT NULL, PRIMARY KEY  (`id`)) ENGINE='
    . _MYSQL_ENGINE_
    . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
