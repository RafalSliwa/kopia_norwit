<?php

use PhpParser\Node\Stmt\TryCatch;
/**
 * Reference is used only for checking if there is correct id_order in 
 * scb_ehp_order_app_mapping record.
 * As POSApplicationNumber is used id_order with time stamp in addition.
 * Construction: id_order_yyyy_MM_dd_hh_mm_ss
 * Something like this: 12_2023_11_25_12_54_45.
 *  id_order = 12
 *  time stamp: 2023-11-25 12:54:45
 */

require_once dirname(__FILE__) . '/../../../config/config.inc.php';

try {
    $query = 'update ' . _DB_PREFIX_ . 'scb_ehp_order_app_mapping oa_map left join ';
    $query = $query . _DB_PREFIX_ . 'orders o on o.reference = oa_map.order_reference set oa_map.id_order = o.id_order';
    Db::getInstance()->execute($query);
    echo '{"isOk":"1"}';
} catch (\Throwable $th) {
    echo '{"isOk":"0"}';
}
