<?php
/**
 * Product.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Carrier
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * Product.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Product extends ProductCore
{
    public static function isAvailableWhenOutOfStock($out_of_stock)
    {
        if ($id_quote_in_cart = (int) Context::getContext()->cookie->__get('ROJA45QUOTATIONSPRO_QUOTEINCART')) {
            require_once _PS_MODULE_DIR_ . 'roja45quotationspro/classes/RojaQuotation.php';
            $quotation = new RojaQuotation($id_quote_in_cart);
            return true;
        } else {
            return parent::isAvailableWhenOutOfStock($out_of_stock);
        }
    }
}
