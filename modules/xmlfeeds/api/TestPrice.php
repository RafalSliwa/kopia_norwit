<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

$_SERVER['REQUEST_URI'] = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/modules/xmlfeeds/api/xml.php?id=0';
$_SERVER['SCRIPT_NAME'] = !empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/modules/xmlfeeds/api/xml.php';
$_SERVER['REQUEST_METHOD'] = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$_SERVER['REMOTE_ADDR'] = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '8.8.8.8';

require_once(dirname(__FILE__).'/../../../config/config.inc.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

if (class_exists('Context', false)) {
    $context = Context::getContext();
    $context->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
    $context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
    Context::getContext()->cart = new Cart();
}

$productId = Tools::getValue('pid');

if (empty($productId)) {
    echo 'Empty product, ID. Should be: TestPrice.php?pid=5';
    die;
}

//pr(Tools::ps_round('403.363785', 2));

$product = new Product($productId, false, 1);

$salePrice1 = Tools::ps_round($product->getPriceStatic($productId, true, null), 2);
$salePrice2 = $product->getPriceStatic($productId, true, null, 2);
$salePrice3 = $product->getPriceStatic($productId, true, null);

echo 'Price1: '.$salePrice1.'<br><br>';
echo 'Price2: '.$salePrice2.'<br><br>';
echo 'Price3: '.$salePrice3.'<br><br>';

die('done');
