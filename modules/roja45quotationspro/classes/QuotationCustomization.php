<?php
/**
 * QuotationCustomization.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationCustomization
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationCustomization.
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

class QuotationCustomization extends ObjectModel
{
    public $id_roja45_quotation_customization;
    public $id_product;
    public $id_product_attribute;
    public $id_customization;

    /**
     * @see ObjectModel::$definition
     *
     * Create a copy of the prestashop customization and assign the product and attribute ids.
     * id_customization available to link to another modules customization if required.
     * This class can be overridden to support other modules.
     *
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_customization',
        'primary' => 'id_roja45_quotation_customization',
        'multilang' => false,
        'fields' => array(
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'id_product_attribute' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'id_customization' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ),
        ),
    );

    public function delete()
    {
        if (Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_customizationdata`
            WHERE `id_roja45_quotation_customization` = ' . (int) $this->id_roja45_quotation_customization)
        ) {
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'customization` WHERE `id_customization` = ' . (int) $this->id_customization
            );
            return parent::delete();
        }
    }

    public function addCustomizationData(
        $id_roja45_quotation_customization,
        $type,
        $index,
        $value,
        $price,
        $weight
    ) {
        $data = array(
            'id_roja45_quotation_customization' => $id_roja45_quotation_customization,
            'type' => $type,
            'index' => $index,
            'value' => $value,
            'price' => $price,
            'weight' => $weight,
        );

        return Db::getInstance()->insert('roja45_quotationspro_customizationdata', $data, true);
    }

    public function getCustomizationData()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_customizationdata');
        $sql->where('id_roja45_quotation_customization=' . (int) $this->id_roja45_quotation_customization);
        return Db::getInstance()->executeS($sql);
    }

    public static function getId($id_customization)
    {
        $sql = new DbQuery();
        $sql->select('id_customization');
        $sql->from('roja45_quotationspro_customization', 'c');
        $sql->where('id_customization = ' . (int) $id_customization);
        return Db::getInstance()->getValue($sql);
    }

    /**
     * Get all customizations.  Filter out those already on the quotation request, return the customization id
     *
     * @param $id_cart
     * @param $id_roja45_quotation_request
     * @param $id_product
     * @param null $id_product_attribute
     * @return int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getProductCustomization(
        $id_cart,
        $id_roja45_quotation_request,
        $id_product,
        $id_product_attribute = null
    ) {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customization', 'c');
        $sql->where('id_cart = ' . (int) $id_cart);
        $sql->where('id_product = ' . (int) $id_product);

        if ($rows = Db::getInstance()->executeS($sql)) {
            $customizations = array();
            foreach ($rows as $row) {
                if ($row['in_cart'] == 0) {
                    $customizations[] = $row['id_customization'];
                }
            }
            $sql = new DbQuery();
            $sql->select('id_customization');
            $sql->from('roja45_quotationspro_requestproduct');
            $sql->where('id_roja45_quotation_request=' . (int) $id_roja45_quotation_request);
            $sql->where('id_product=' . (int) $id_product);
            $sql->where('id_product_attribute=' . (int) $id_product_attribute);
            $sql->where('id_customization>0');

            if ($requested_customizations = Db::getInstance()->executeS($sql)) {
                foreach ($requested_customizations as $requested_customization) {
                    $quotation_customization = new QuotationCustomization($requested_customization['id_customization']);
                    if (($key = array_search($quotation_customization->id_customization, $customizations)) !== false) {
                        unset($customizations[$key]);
                    }
                }
            }
            // SHould only have one customiztion now.
            if (count($customizations)) {
                $id_customization = (int) array_shift($customizations);
                $customization = new Customization($id_customization);
                $customization->id_product_attribute = $id_product_attribute;
                $customization->in_cart = 1;
                $customization->save();
                return $id_customization;
            } else {
                return 0;
            }
        }
        return 0;
    }

    public static function createCustomization($id_customization)
    {
        $customization = new Customization($id_customization);
        $quotation_customization = new QuotationCustomization();
        $quotation_customization->id_product = $customization->id_product;
        $quotation_customization->id_product_attribute = $customization->id_product_attribute;
        $quotation_customization->id_customization = $id_customization;
        $quotation_customization->save();

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customized_data');
        $sql->where('id_customization=' . (int) $id_customization);
        if ($datas = Db::getInstance()->executeS($sql)) {
            foreach ($datas as $data) {
                if ($data['type'] == Product::CUSTOMIZE_FILE) {
                    if (file_exists(_PS_UPLOAD_DIR_ . $data['value'])) {
                        copy(
                            _PS_UPLOAD_DIR_ . $data['value'],
                            _PS_UPLOAD_DIR_ . 'roja_' . $data['value']
                        );
                    }
                    if (file_exists(_PS_UPLOAD_DIR_ . $data['value'] . '_small')) {
                        copy(
                            _PS_UPLOAD_DIR_ . $data['value'] . '_small',
                            _PS_UPLOAD_DIR_ . 'roja_' . $data['value'] . '_small'
                        );
                    }
                    $data['value'] = 'roja_' . $data['value'];
                }
                $quotation_customization->addCustomizationData(
                    $quotation_customization->id,
                    $data['type'],
                    $data['index'],
                    $data['value'],
                    $data['price'],
                    $data['weight']
                );
            }
        }

        return $quotation_customization->id;
    }

    public static function createCartCustomization(
        $id_cart,
        $qty,
        $id_roja45_quotation_customization,
        $id_address_delivery
    ) {
        $quotation_customization = new QuotationCustomization($id_roja45_quotation_customization);
        $customization = new Customization();
        $customization->id_cart = $id_cart;
        $customization->id_address_delivery = $id_address_delivery;
        $customization->id_product = $quotation_customization->id_product;
        $customization->id_product_attribute = $quotation_customization->id_product_attribute;
        //$customization->quantity = $qty;
        $customization->quantity = 0;
        $customization->quantity_refunded = 0;
        $customization->quantity_returned = 0;
        $customization->in_cart = 1;
        $customization->save();

        if ($datas = $quotation_customization->getCustomizationData()) {
            foreach ($datas as $data) {
                $data = array(
                    'id_customization' => $customization->id,
                    'type' => $data['type'],
                    'index' => $data['index'],
                    'value' => $data['value'],
                    'id_module' => 0,
                    'price' => $data['price'],
                    'weight' => $data['weight'],
                );
                Db::getInstance()->insert('customized_data', $data, true);
            }
        }

        return $customization->id;
    }

    public static function getCustomizations(
        $id_product,
        $id_product_attribute,
        $id_roja45_quotation_customization,
        $id_lang = null,
        $id_shop = null
    ) {
        if (!$id_shop) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        }
        $sql = new DbQuery();
        $sql->select(
            'c.id_roja45_quotation_customization, c.`id_customization`, c.`id_product`, c.`id_product_attribute`,
            cd.`type`, cd.`index`, cd.`value`'
        );
        $sql->from('roja45_quotationspro_customization', 'c');
        $sql->leftJoin(
            'roja45_quotationspro_customizationdata',
            'cd',
            'c.id_roja45_quotation_customization = cd.id_roja45_quotation_customization'
        );
        if ($id_lang) {
            $sql->select(
                'cfl.`id_customization_field`, cfl.`name`'
            );
            $sql->leftJoin(
                'customization_field_lang',
                'cfl',
                '(cfl.id_customization_field = cd.`index`
                AND id_lang = ' . (int) $id_lang . ($id_shop ? '
                AND cfl.`id_shop` = ' . (int) $id_shop : '') . ')'
            );
        }

        $sql->where('c.id_product=' . (int) $id_product);
        $sql->where('c.id_product_attribute=' . (int) $id_product_attribute);
        $sql->where('c.id_roja45_quotation_customization=' . (int) $id_roja45_quotation_customization);
        $rows = Db::getInstance()->executeS($sql);
        foreach ($rows as &$row) {
            if ($row['type'] == 0) {
                $row['image'] = '/upload/' . $row['value'];
                $row['image_small'] = '/upload/' . $row['value'] . '_small';
            }
        }
        return $rows;
    }
}
