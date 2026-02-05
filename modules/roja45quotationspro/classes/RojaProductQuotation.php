<?php
/**
 * RojaProductQuotation.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaProductQuotation
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaProductQuotation.
 * 2023 TOOLE - Inter-soft.com
 * All rights reserved.
 *
 * DISCLAIMER
 *
 * Changing this file will render any support provided by us null and void.
 *
 * @author    Toole <support@toole.com>
 * @copyright 2023 TOOLE - Inter-soft.com
 * @license   license.txt
 * @category  TooleAmazonMarketTool
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class RojaProductQuotation extends ObjectModel
{
    public $id_product;
    public $id_shop;
    public $enabled;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_quotationspro',
        'primary' => 'id_roja45_product_quotation',
        'multilang' => false,
        'multishop' => false,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'enabled' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true, 'shop' => false)
        ),
    );

    public function save($null_values = false, $auto_date = true)
    {
        //Shop::addTableAssociation('product_quotationspro', array('type' => 'shop'));
        return parent::save($null_values, $auto_date);
    }

    public function delete()
    {
        //Shop::addTableAssociation('product_quotationspro', array('type' => 'shop'));
        return parent::delete();
    }

    public static function updateEnabled($id_product, $enabled = null, $id_shop = null, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $shop_ids = array();
        if (!$id_shop) {
            if (Shop::getContext() == Shop::CONTEXT_ALL) {
                $shops = Shop::getShops(true);
                foreach ($shops as $shop) {
                    $shop_ids[] = $shop['id_shop'];
                }
            } else {
                $shop_ids[] = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ?
                    (int)$context->shop->id : Configuration::get('PS_SHOP_DEFAULT');
            }
        }

        $return = true;
        foreach ($shop_ids as $shop_id) {
            $sql = new DbQuery();
            $sql->select('pq.id_roja45_product_quotation');
            $sql->from('product_quotationspro', 'pq');

            /*
            $sql->leftJoin(
                'product_quotationspro_shop',
                'pqs',
                'pq.id_roja45_product_quotation = pqs.id_roja45_product_quotation'
            );*/
            $sql->where('pq.id_product='. (int) $id_product);
            $sql->where('pq.id_shop='. (int) $shop_id);
            $id_roja45_product_quotation = (int) Db::getInstance()->getValue($sql);

            if (!is_numeric($enabled) && !$id_roja45_product_quotation) {
                $enabled = 1;
            } elseif (!is_numeric($enabled) && $id_roja45_product_quotation) {
                $product_quotation = new RojaProductQuotation($id_roja45_product_quotation);
                $enabled = (int) !$product_quotation->enabled;
            }
            $product_quotation = new RojaProductQuotation($id_roja45_product_quotation);
            $product_quotation->id_product = (int) $id_product;
            $product_quotation->id_shop = (int) $shop_id;
            $product_quotation->enabled = (int) $enabled;
            $return &= $product_quotation->save();
        }
        return $return;
    }

    public static function enableAll($id_shop = null)
    {
        if (!$id_shop) {
            $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ?
                (int)Context::getContext()->shop->id : Configuration::get('PS_SHOP_DEFAULT');
        }

        $sql = 'UPDATE '._DB_PREFIX_.'product_quotationspro a 
        SET a.enabled= 1
        WHERE a.id_shop='.(int) $id_shop;
        return Db::getInstance()->execute($sql);
    }
}
