<?php
/**
 * NOTICE OF LICENSE.
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2024, Ceneo
 * @license   LICENSE.txt
 */
declare(strict_types=1);

namespace CeneoBs\Service;

use Configuration as Cfg;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdditionalServiceProduct
{
    protected $reference;
    protected $name;
    protected $price;

    public function __construct($reference, $name, $price)
    {
        $this->reference = $reference;
        $this->name = $name;
        $this->price = $price;
    }

    public function checkIfProductExist($reference)
    {
        $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($reference) . '"';
        return \Db::getInstance()->getValue($sql);
    }

    public function getProductIdByReference($reference)
    {
        $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($reference) . '"';
        return \Db::getInstance()->getValue($sql);
    }

    public function createOrUpdateProduct(): void
    {
        if (!$this->checkIfProductExist($this->reference)) {
            $product = new \Product();
            $product->name = [Cfg::get('PS_LANG_DEFAULT') => $this->name];
            $product->price = $this->price;
            $product->id_tax_rules_group = 0;
            $product->active = 1;
            $product->visibility = 'none';
            $product->reference = $this->reference;
            $product->add();
            \StockAvailable::setQuantity($product->id, 0, 999999);
        } else {
            $productId = $this->getProductIdByReference($this->reference);
            $product = new \Product($productId);
            $product->price = $this->price;
            $product->id_tax_rules_group = 0;
            $product->active = 1;
            $product->visibility = 'none';
            $product->update();
            \StockAvailable::setQuantity($product->id, 0, 999999);
        }
    }

    public function addToCart($cart, $productReference)
    {
        $productId = $this->getProductIdByReference($productReference);
        $quantity = 1;

        if (!$cart->updateQty($quantity, $productId)) {
            echo 'Failed to add product to cart.';
        }
    }
}
