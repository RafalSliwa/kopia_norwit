<?php
/**
 * RojaQuotation.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaQuotation
 *
 * @link      https://toolecommerce.com/
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaQuotation.
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

class RojaQuotation extends ObjectModel
{
    public $id_roja45_quotation;
    public $id_roja45_quotation_status;
    public $id_lang;
    public $id_shop;
    public $id_currency;
    public $id_country;
    public $id_state;
    public $id_address_invoice;
    public $id_address_delivery;
    public $id_address_tax;
    public $id_carrier;
    public $id_request;
    public $valid_days;
    public $expiry_date;
    public $email;
    public $firstname;
    public $lastname;
    public $form_data;
    public $reference;
    public $calculate_taxes;
    public $modified;
    public $quote_sent;
    public $id_customer;
    public $tmp_password;
    public $id_cart;
    public $id_order;
    public $purchase_date;
    public $id_employee;
    public $id_profile;
    public $total_to_pay;
    public $total_to_pay_wt;
    public $total_products;
    public $total_products_wt;
    public $total_shipping_exc;
    public $total_shipping_inc;
    public $total_handling;
    public $total_handling_wt;
    public $total_wrapping;
    public $total_discount;
    public $total_discount_wt;
    public $total_charges;
    public $total_charges_wt;
    public $filename;
    public $is_template;
    public $quote_name;
    public $template_name;
    public $date_add;
    public $date_upd;

    private $is_dirty;
    private $quotation_total_shipping;
    private $quotation_total_handling;
    private $quotation_total_charges;
    private $quotation_total_discount;
    private $quotation_total_products;
    private $quotation_total;

    const ROUND_ITEM = 1;
    const ROUND_LINE = 2;
    const ROUND_TOTAL = 3;

    private static $totals_cache = array();
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro',
        'primary' => 'id_roja45_quotation',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation_status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_state' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_address_invoice' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_address_delivery' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_address_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_request' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'valid_days' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'expiry_date' => array('type' => self::TYPE_DATE, 'required' => false),
            'email' => array(
                'type' => self::TYPE_STRING,
                'size' => 128,
                'required' => false,
            ),
            'firstname' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isName',
                'size' => 128,
                'required' => false,
            ),
            'lastname' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isName',
                'size' => 128,
                'required' => false,
            ),
            'form_data' => array('type' => self::TYPE_STRING),
            'reference' => array('type' => self::TYPE_STRING),
            'calculate_taxes' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'modified' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'quote_sent' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'tmp_password' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 8),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false),
            'purchase_date' => array('type' => self::TYPE_DATE, 'required' => false),
            'id_profile' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'filename' => array('type' => self::TYPE_STRING),
            'total_to_pay' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_to_pay_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_products' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_products_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_exc' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_inc' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_handling' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_handling_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_wrapping' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_discount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_discount_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_charges' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_charges_wt' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'is_template' => array('type' => self::TYPE_BOOL),
            'quote_name' => array('type' => self::TYPE_STRING),
            'template_name' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'quotation',
        'objectsNodeName' => 'quotations',
        'fields' => array(
            'id_lang' => array('xlink_resource' => 'languages'),
            'id_currency' => array('xlink_resource' => 'currencies'),
            'id_address_invoice' => array('xlink_resource' => 'addresses'),
            'id_address_delivery' => array('xlink_resource' => 'addresses'),
            'id_cart' => array('xlink_resource' => 'carts'),
            'id_customer' => array('xlink_resource' => 'customers'),
            'id_carrier' => array('xlink_resource' => 'carriers'),
            'expiry_date' => array(),
            'firstname' => array(),
            'lastname' => array(),
            'email' => array(),
            'reference' => array(),
            'quote_name' => array(),
            'date_add' => array(),
            'date_upd' => array(),
        ),
        'associations' => array(
            'quotation_products' => array(
                'resource' => 'quotation_product',
                'setter' => false,
                'virtual_entity' => true,
                'fields' => array(
                    'id' => array(),
                    'id_product' => array('xlink_resource' => 'products'),
                    'id_product_attribute' => array(),
                    'qty' => array(),
                    'name' => array(),
                    'date_add' => array(),
                    'date_upd' => array(),
                )),
            'quotation_orders' => array(
                'resource' => 'quotation_order',
                'setter' => false,
                'virtual_entity' => true,
                'fields' => array(
                    'id' => array(),
                    'id_order' => array('xlink_resource' => 'orders'),
                    'date_add' => array(),
                    'date_upd' => array(),
                )),
        ),
    );

    const ONLY_PRODUCTS = 1;
    const ONLY_DISCOUNTS = 2;
    const ONLY_CHARGES = 9;
    const BOTH = 3;
    const BOTH_WITHOUT_SHIPPING = 4;
    const ONLY_SHIPPING = 5;
    const ONLY_WRAPPING = 6;
    const ONLY_PRODUCTS_WITHOUT_SHIPPING = 7;
    const ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING = 8;
    const TAX_RATE = 10;
    const TOTAL_BEFORE_DISCOUNT = 11;
    const TOTAL_AFTER_DISCOUNT = 12;
    const ONLY_HANDLING = 13;
    const TAX_INVOICE_ADDRESS = 21;
    const TAX_DELIVERY_ADDRESS = 22;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getQuotations($id_lang = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        if (!$id_shop) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        }
        $cache_id = 'QuotationStatus::getQuotations' . $id_lang . '-' . $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('q.*');
            $sql->from('roja45_quotationspro', 'q');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
        }
        return Cache::retrieve($cache_id);
    }

    public function isRemovable()
    {
        return true;
    }

    public function isLocked()
    {
        return false;
    }

    public function deleteAddress($id)
    {
        $address = new Address($id);
        $address->delete();
    }

    public function getCarriers()
    {
        $_carriers = array();
        $address = $this->getTaxAddress();
        $country = new Country($address->id_country);
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS')) {
            $id_zone = false;
        } else {
            $id_zone = $country->id_zone;
            if ($address->id_state) {
                $state = new State($address->id_state);
                $id_zone = $state->id_zone;
            }
        }

        $products = $this->getProducts();

        if ((int) $this->id_customer) {
            $customer = new Customer((int) $this->id_customer);
            $carriers = Carrier::getCarriers(
                (int) Configuration::get('PS_LANG_DEFAULT'),
                true,
                false,
                (int) $id_zone,
                $customer->getGroups(),
                Carrier::ALL_CARRIERS
            );
            unset($customer);
        } else {
            $carriers = Carrier::getCarriers(
                (int) Configuration::get('PS_LANG_DEFAULT'),
                true,
                false,
                (int) $id_zone,
                null,
                Carrier::ALL_CARRIERS
            );
        }
        $product_carriers = [];
        foreach ($products as $product) {
            $product = new Product($product['id_product']);
            //$product_carriers = array_merge($product_carriers, $product->getCarriers());
            foreach ($product->getCarriers() as $product_carrier) {
                if (!in_array($product_carrier['id_carrier'], $product_carriers)) {
                    $product_carriers[] = $product_carrier['id_carrier'];
                }
            }
        }
        $total = $this->getQuotationTotal(true, Cart::ONLY_PRODUCTS);
        foreach ($carriers as $k => $row) {
            if (count($product_carriers) && !in_array($row['id_carrier'], $product_carriers)) {
                unset($carriers[$k]);
                continue;
            }
            /** @var Carrier $carrier */
            $_carriers[$row['id_carrier']]['carrier'] = new Carrier((int) $row['id_carrier']);
            $carrier = $_carriers[$row['id_carrier']]['carrier'];
            $shipping_method = $carrier->getShippingMethod();
            // Get only carriers that are compliant with shipping method
            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT &&
                $carrier->getMaxDeliveryPriceByWeight((int) $id_zone) === false
            ) || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE &&
                $carrier->getMaxDeliveryPriceByPrice((int) $id_zone) === false
            )) {
                unset($carriers[$k]);
                continue;
            }

            // If out-of-range behavior carrier is set on "Desactivate carrier"
            if ($row['range_behavior']) {
                $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
                    $row['id_carrier'],
                    $this->getTotalWeight(),
                    (int) $id_zone
                );
                $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
                    $row['id_carrier'],
                    $total,
                    (int) $id_zone,
                    (int) $this->id_currency
                );
                // Get only carriers that have a range compatible with cart
                if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !$check_delivery_price_by_weight)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !$check_delivery_price_by_price)
                ) {
                    unset($carriers[$k]);
                    continue;
                }
            }

            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping = $carrier->getDeliveryPriceByWeight(
                    $this->getTotalWeight($products),
                    (int) $id_zone
                );
            } else {
                $shipping = $carrier->getDeliveryPriceByPrice($total, (int) $id_zone, (int) $this->id_currency);
            }

            $_carriers[$row['id_carrier']]['shipping'] = $shipping;
            $currency = new Currency($this->id_currency);
            $_carriers[$row['id_carrier']]['shipping_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                (float) Tools::convertPrice(
                    $shipping,
                    $currency,
                    true
                ),
                $currency
            );
        }
        $return = array();
        $return['carriers'] = $_carriers;

        return $return;
    }

    public function getTotalWeight($products = null)
    {
        $_totalWeight = 0;
        if (null !== $products) {
            foreach ($products as $product) {
                if (Combination::isFeatureActive() && $product['id_product_attribute']) {
                    $weight_product = Db::getInstance()->getValue('
                    SELECT SUM((p.`weight` + pa.`weight`)) as nb
                    FROM `' . _DB_PREFIX_ . 'product` p
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.`id_product_attribute` = ' . $product['id_product_attribute'] . '
                    WHERE (pa.`id_product_attribute` IS NOT NULL AND pa.`id_product_attribute` != 0)
                    AND p.`id_product` = ' . (int)$product['id_product']
                    );
                    $weight_product *= $product['qty'];
                } else {
                    $weight_product = Db::getInstance()->getValue('
                SELECT p.`weight`
                FROM `' . _DB_PREFIX_ . 'product` p
                WHERE p.`id_product` = ' . (int)$product['id_product']
                    );
                    $weight_product *= $product['qty'];
                }

                $_totalWeight += round(
                    (float)$weight_product,
                    3
                );
            }
        }

        return $_totalWeight;
    }

    public function getTaxesAverage()
    {
        if (!Configuration::get('PS_TAX')) {
            return 0;
        }

        $products = $this->getProducts();
        $total_products_moy = 0;
        $ratio_tax = 0;

        if (!count($products)) {
            return 0;
        }

        $address = $this->getTaxAddress();
        foreach ($products as $product) {
            // products refer to the cart details
            $tax_manager = TaxManagerFactory::getManager(
                $address,
                Product::getIdTaxRulesGroupByIdProduct((int) $product['id_product']),
                Context::getContext()
            );
            $product_tax_calculator = $tax_manager->getTaxCalculator();

            $total_products_moy += $product['unit_price_tax_excl'];
            $ratio_tax += $product['unit_price_tax_excl'] * $product_tax_calculator->getTotalRate();
        }

        if ($total_products_moy > 0) {
            return $ratio_tax / $total_products_moy;
        }

        return 0;
    }

    public static function getQuotationsForCustomer($id_customer)
    {
        $id_deleted_status = (int) Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_DLTD');
        $sql = new DbQuery();
        $sql->select('q.id_roja45_quotation');
        $sql->from('roja45_quotationspro', 'q');
        $sql->where('q.id_customer = ' . (int) $id_customer);
        $sql->where('q.id_roja45_quotation_status != ' . (int) $id_deleted_status);
        $sql->orderBy('q.date_add DESC');
        return Db::getInstance()->executeS($sql);
    }

    public static function getQuotationForCart($id_cart)
    {
        $id_deleted_status = (int) Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_DLTD');
        $sql = new DbQuery();
        $sql->select('q.id_roja45_quotation');
        $sql->from('roja45_quotationspro', 'q');
        $sql->where('q.id_cart = ' . (int) $id_cart);
        $sql->where('q.id_roja45_quotation_status != ' . (int) $id_deleted_status);
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getQuotationsForStatus($status_code)
    {
        if ($id_roja45_quotation_status = QuotationStatus::getQuotationStatusByType($status_code)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('roja45_quotationspro', 'q');
            $sql->where('q.id_roja45_quotation_status = ' . (int) $id_roja45_quotation_status);
            $sql->orderBy('q.date_add DESC');
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    public static function getQuotationsSince($time)
    {
        $date_to_check = date('Y-m-d H:i:s', $time);
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro', 'q');
        $sql->where('q.date_add >= "' . $date_to_check . '"');
        $sql->where('q.id_roja45_quotation_status = ' . (int) QuotationStatus::getQuotationStatusByType(
            QuotationStatus::$RCVD
        ));
        $sql->orderBy('q.date_add DESC');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getQuotationForRequest($id_roja45_quotation_request)
    {
        $sql = '
            SELECT id_roja45_quotation
            FROM `' . _DB_PREFIX_ . 'roja45_quotationspro` q
            WHERE q.id_request = ' . (int) $id_roja45_quotation_request;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getQuotationForReference($reference)
    {
        $sql = new DbQuery();
        $sql->select('id_roja45_quotation');
        $sql->from('roja45_quotationspro', 'q');
        $sql->where('q.reference="' . pSQL($reference) . '"');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getNumberOfProducts()
    {
        $sql = new DbQuery();
        $sql->select('count(id_roja45_quotation) as count');
        $sql->from('roja45_quotationspro_product', 'qp');
        $sql->where('qp.`id_roja45_quotation` = ' . (int) $this->id);
        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getProducts($id_lang = null, $id_currency = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = $this->id_lang;
        }
        if (!$id_shop) {
            $id_shop = $this->id_shop;
        }
        if (!$id_currency) {
            $id_currency = $this->id_currency;
        }

        $defaultCurrency = Currency::getDefaultCurrency();

        $sql = new DbQuery();

        $sql->select('*');
        $sql->from('roja45_quotationspro_product', 'qp');
        $sql->leftJoin('product', 'p', 'p.`id_product` = qp.`id_product`');
        $sql->innerJoin(
            'product_shop',
            'product_shop',
            'product_shop.`id_shop` = qp.`id_shop` AND product_shop.`id_product` = p.`id_product` AND product_shop.`id_shop` = ' . (int) $id_shop
        );
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $id_lang . ' AND pl.`id_shop` = ' . (int) $id_shop
        );
        $sql->where('qp.`id_roja45_quotation` = ' . (int) $this->id);
        $sql->orderBy('qp.`position` ASC');
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $address = $this->getTaxAddress();
        $currency = Currency::getCurrencyInstance($id_currency);
        $resultArray = array();
        foreach ($products as $id => &$product) {
            $productObj = new Product($product['id_product']);
            if (Validate::isLoadedObject($productObj)) {
                $key = $product['id_product'] . '_' . $product['id_product_attribute'] . '_' . $product['id_customization'];
                $id_product_attribute = null;
                $id_image = 0;
                //$product['name'] = htmlspecialchars($product['name']);
                $product['product_title'] = $product['name'];
                $product['attributes'] = null;

                $product['product_url'] = Context::getContext()->link->getProductLink(
                    $product['id_product'],
                    null,
                    null,
                    null,
                    Context::getContext()->language->id,
                    Context::getContext()->shop->id,
                    $product['id_product_attribute']
                );
                
                if ($product['id_product_attribute'] > 0) {
                    $id_product_attribute = $product['id_product_attribute'];
                    $combination = new Combination($product['id_product_attribute']);
                    $combo_str = '';
                    $combination_names = $combination->getAttributesName($id_lang);
                    foreach ($combination_names as $combination_name) {
                        $combo_str = $combo_str . ' [' . $combination_name['name'] . ']';
                    }
                    $images = Image::getImages($id_lang, $product['id_product'], $product['id_product_attribute']);
                    if (count($images)) {
                        $id_image = $images[0]['id_image'];
                    } else {
                        if ($id_images = Product::getCover($product['id_product'], Context::getContext())) {
                            $id_image = $id_images['id_image'];
                        } else {
                            $id_image = Context::getContext()->language->iso_code . '-default';
                        }
                    }
                    if ((int) $combination->ecotax) {
                        $product['ecotax'] = $combination->ecotax;
                    }

                    $product['wholesale_price'] = $combination->wholesale_price;
                    $product['wholesale_price_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        (float) Tools::convertPrice(
                            $product['wholesale_price'],
                            $currency,
                            true
                        ),
                        $currency
                    );

                    $attributes = Db::getInstance()->executeS(
                        ' SELECT pac.`id_product_attribute`,
                        agl.`public_name` AS public_group_name, al.`name` AS attribute_name
                        FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (
                            a.`id_attribute` = al.`id_attribute`
                            AND al.`id_lang` = ' . (int) $this->id_lang . '
                        )
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (
                            ag.`id_attribute_group` = agl.`id_attribute_group`
                            AND agl.`id_lang` = ' . (int) $this->id_lang . '
                        )
                        WHERE pac.`id_product_attribute` = ' . $product['id_product_attribute'] . '
                        ORDER BY ag.`position` ASC, a.`position` ASC'
                    );

                    if (count($attributes)) {
                        $product['attributes'] = '';
                        $product['attributes_small'] = '';
                    }
                    foreach ($attributes as $attribute) {
                        $product['attributes'] .= $attribute['public_group_name'] . ' : ' . $attribute['attribute_name'] . ', ';
                        $product['attributes_small'] .= $attribute['attribute_name'] . ', ';
                    }

                    if (!empty($combination->reference)) {
                        $product['reference'] = $combination->reference;
                    }
                } else {
                    if ($id_images = Product::getCover($product['id_product'], Context::getContext())) {
                        $id_image = $id_images['id_image'];
                    } else {
                        $id_image = Context::getContext()->language->iso_code . '-default';
                    }

                    $product['wholesale_price_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        Tools::convertPrice(
                            $product['wholesale_price'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                }

                $product['id_address_invoice'] = null;
                $product['id_address_delivery'] = null;
                $product['quantity'] = $product['qty'];
                $product['cart_quantity'] = $product['qty'];

                if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                    $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('small');
                } else {
                    $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('medium');
                }
                $image_size = Image::getSize($format);

                if (!empty($product['custom_image']) && file_exists(_PS_ROOT_DIR_ . $product['custom_image'])) {
                    $legend = 'Custom Image';

                    $link = Context::getContext()->link->getBaseLink();
                    $product['image_tag'] = $link . $product['custom_image'];
                    $product['image_url'] = $link . $product['custom_image'];
                } else {
                    $imageObj = new Image($id_image, (int) Context::getContext()->language->id);
                    if (!Validate::isLoadedObject($imageObj)) {
                        $id_image = Context::getContext()->language->iso_code . '-default';
                        $legend = 'No picture';
                    } else {
                        $legend = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($imageObj->legend))))));
                    }

                    $product['image'] = $imageObj;
                    $product['id_image'] = $id_image;

                    $product['image_tag'] = Context::getContext()->link->getImageLink(
                        $product['link_rewrite'],
                        $id_image,
                        $format
                    );

                    $product['image_url'] = Context::getContext()->link->getImageLink(
                        $product['link_rewrite'],
                        $id_image,
                        $format
                    );
                    $protocol_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';

                    $product['image_missing'] = 0;
                    $path = _PS_PROD_IMG_DIR_ . $imageObj->getImgPath() . '.' . $imageObj->image_format;
                    if (!file_exists($path)) {
                        $product['image_missing'] = 1;
                    }
                    $product['image_loc'] = $path;
                }
                $product['image_width'] = $image_size['width'];
                $product['image_height'] = $image_size['height'];
                $product['image_legend'] = $legend;

                if (isset($this->id_customer) && $this->id_customer) {
                    $id_group = (int) Customer::getDefaultGroupId($this->id_customer);
                } else {
                    $id_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
                }

                $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
                    (int) $product['id_product'],
                    Context::getContext()
                );
                $product_tax_calculator = TaxManagerFactory::getManager(
                    $address,
                    $id_tax_rules_group
                )->getTaxCalculator();
                $product['tax_rate'] = $product_tax_calculator->getTotalRate();
                $product['tax_rate_formatted'] = $product['tax_rate'] . '%';

                $null = null;
                $product['list_price_excl'] = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $id_product_attribute,
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    1,
                    false, /* USE TAX*/
                    6,
                    false,
                    true,
                    false, /* WITH ECOTAX */
                    $null,
                    true,
                    0,
                    true,
                    null,
                    1
                );
                if (!$product['list_price_excl'] || empty($product['list_price_excl'])) {
                    $product['list_price_excl'] = 0;
                }
                $product['list_price_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    $product['list_price_excl'],
                    $currency
                );

                $product['ecotax_inc'] = $product['ecotax'];
                if ($product['ecotax'] > 0) {
                    $product['ecotax_inc'] *= (1 + Tax::getProductEcotaxRate() / 100);
                }


                $product['list_price_excl_without_reduction'] = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $id_product_attribute,
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    1,
                    false, /* USE TAX*/
                    6,
                    false,
                    false,
                    false, /* WITH ECOTAX */
                    $null,
                    !((bool) Configuration::get('ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP')),
                    0,
                    true,
                    null,
                    1
                );
                $product['list_price_excl_without_reduction_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['list_price_excl_without_reduction'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['list_price_incl'] = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $id_product_attribute,
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    1,
                    true, /* USE TAX*/
                    6,
                    false,
                    true,
                    false, /* WITH ECOTAX */
                    $null,
                    true,
                    0,
                    true,
                    null,
                    1
                );
                if (!$product['list_price_incl'] || empty($product['list_price_incl'])) {
                    $product['list_price_incl'] = 0;
                }
                $product['list_price_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['list_price_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['list_price_incl_without_reduction'] = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $id_product_attribute,
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    1,
                    true, /* USE TAX*/
                    6,
                    false,
                    false,
                    false, /* WITH ECOTAX */
                    $null,
                    true,
                    0,
                    true,
                    null,
                    1
                );
                $product['list_price_incl_without_reduction_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['list_price_incl_without_reduction'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['list_price_without_reduction_tax'] =
                    $product['list_price_incl_without_reduction'] - $product['list_price_excl_without_reduction'];
                $product['list_price_without_reduction_tax_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['list_price_without_reduction_tax'],
                        $currency,
                        true
                    ),
                    $currency
                );

                if ($product['customization_cost_type'] == 1) {
                    $product['customization_cost_total_exc'] = $product['customization_cost_exc'];
                    $product['customization_cost_total_inc'] = $product['customization_cost_inc'];
                } else {
                    $product['customization_cost_total_exc'] = $product['customization_cost_exc'] * $product['qty'];
                    $product['customization_cost_total_inc'] = $product['customization_cost_inc'] * $product['qty'];
                }

                $product['product_price_list_subtotal_excl'] = ($product['list_price_excl'] * $product['qty']) + $product['customization_cost_total_exc'];
                $product['product_price_list_subtotal_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_list_subtotal_excl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_price_list_subtotal_incl'] = ($product['list_price_incl'] * $product['qty']) + $product['customization_cost_total_inc'];
                $product['product_price_list_subtotal_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_list_subtotal_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_price_list_tax'] = $product['list_price_incl'] - $product['list_price_excl'];
                $product['product_price_list_tax_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_list_tax'],
                        $currency,
                        true
                    ),
                    $currency
                );

                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                    && (int) $product['advanced_stock_management'] == 1
                    && isset($product['id_warehouse'])
                    && (int) $product['id_warehouse'] > 0
                ) {
                    $product['current_stock'] = StockManagerFactory::getManager()->getProductPhysicalQuantities(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        (int) $product['id_warehouse'],
                        true
                    );
                } else {
                    $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        (int) $this->id_shop
                    );
                }

                if (isset($product['download_hash']) && !empty($product['download_hash'])) {
                    $product['filename'] = ProductDownload::getFilenameFromIdProduct((int) $product['id_product']);
                    $product['display_filename'] = ProductDownload::getFilenameFromFilename($product['filename']);
                }
                $id_image = null;
                $product['unit_price_tax_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    $product['unit_price_tax_excl'],
                    $currency
                );
                $product['product_price_formatted'] = $product['unit_price_tax_excl_formatted'];
                $product['unit_price_tax_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['unit_price_tax_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_price_inc_formatted'] = $product['unit_price_tax_incl_formatted'];

                $product['product_price_subtotal_excl'] = ($product['unit_price_tax_excl'] * $product['qty']) + $product['customization_cost_total_exc'];

                $product['product_price_subtotal_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_subtotal_excl'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_price_subtotal_incl'] = ($product['unit_price_tax_incl'] * $product['qty']) + $product['customization_cost_total_inc'];
                $product['product_price_subtotal_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_subtotal_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_profit_subtotal_excl'] =
                    ($product['unit_price_tax_excl'] - $product['wholesale_price']) * $product['qty'];
                $product['product_profit_subtotal_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_profit_subtotal_excl'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_profit_subtotal_incl'] =
                    ($product['unit_price_tax_incl'] - $product['wholesale_price']) * $product['qty'];
                $product['product_profit_subtotal_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_profit_subtotal_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['tax_paid'] = $product['product_price_subtotal_incl'] - $product['product_price_subtotal_excl'];
                $product['tax_paid_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['tax_paid'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_price_deposit_excl'] =
                    $product['product_price_subtotal_excl'] * ($product['deposit_amount'] / 100);
                $product['product_price_deposit_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_deposit_excl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_price_deposit_incl'] =
                    $product['product_price_subtotal_incl'] * ($product['deposit_amount'] / 100);
                $product['product_price_deposit_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_deposit_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                if ($product['id_customization']) {
                    $product['customizations'] = QuotationCustomization::getCustomizations(
                        $product['id_product'],
                        $product['id_product_attribute'],
                        $product['id_customization'],
                        $this->id_lang
                    );
                } else {
                    $product['customizations'] = array();
                }

                $product['additional_shipping_cost'] = $product['additional_shipping_cost'] * $product['qty'];
                $product['additional_shipping_cost_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['additional_shipping_cost'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['deleted'] = false;
                $resultArray[$key] = $product;
            } else {
                $product['deleted'] = true;
                $resultArray[$id] = $product;
            }
        }

        return $resultArray;
    }

    public function getQuotationProductList()
    {
        return QuotationProduct::getList($this->id);
    }

    public function getQuotationOrderList()
    {
        return QuotationOrder::getList($this->id);
    }

    public function getQuotationChargeList($type = null)
    {
        return QuotationCharge::getList($this->id, $type);
    }

    public function getQuotationAllCharges()
    {
        $results = QuotationCharge::getAllCharges($this->id);
        $currency = new Currency($this->id_currency);
        foreach ($results as &$row) {
            $row['charge_amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $row['charge_amount'],
                    $currency,
                    true
                ),
                $currency
            );
            $row['charge_amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $row['charge_amount_wt'],
                    $currency,
                    true
                ),
                $currency
            );
        }
        return $results;
    }

    public function getQuotationShippingCharges($id_lang)
    {
        $results = QuotationCharge::getAllCharges($this->id);
        $currency = new Currency($this->id_currency);
        foreach ($results as &$row) {
            if ($row['charge_type'] == QuotationCharge::$SHIPPING) {
                $row['charge_amount_currency'] = Tools::ps_round(
                    Tools::convertPrice(
                        $row['charge_amount'],
                        $currency,
                        true
                    ),
                    2
                );
                $row['charge_amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $row['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $row['charge_amount_wt_currency'] = Tools::ps_round(
                    Tools::convertPrice(
                        $row['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    2
                );
                $row['charge_amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $row['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $row['charge_handling_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $row['charge_handling'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $row['charge_handling_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $row['charge_handling_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $row['free_shipping'] = 0;
                if ($row['id_carrier']) {
                    $id_lang = $this->id_lang;
                    if (!$this->id_lang) {
                        $id_lang = Configuration::get('PS_LANG_DEFAULT');
                    }
                    $carrier = new Carrier(
                        $row['id_carrier'],
                        $id_lang
                    );
                    $row['delay'] = $carrier->delay;
                    if ($carrier->is_free) {
                        $row['free_shipping'] = 1;
                    }
                }
            }
        }
        return $results;
    }

    public function getQuotationOrders()
    {
        return QuotationOrder::getOrders($this->id);
    }

    public function getQuotationAllDiscounts()
    {
        $results = QuotationCharge::getAllDiscounts($this->id);
        $currency = new Currency($this->id_currency);
        foreach ($results as &$row) {
            if ($row['charge_method'] == QuotationCharge::$VALUE) {
                $row['charge_amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $row['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $row['charge_amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $row['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $row['charge_value_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    $row['charge_value'],
                    $currency
                );
            }
        }
        return $results;
    }

    public function getQuotationNotesList()
    {
        return QuotationNote::getList($this->id);
    }

    public function addDocument($display_name, $file, $internal_name, $file_type, $id_document = 0)
    {
        $sql =
        'INSERT INTO `' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document`
            (`id_roja45_quotation_document`,`id_roja45_quotation`,`id_roja45_document`,`display_name`,`file_type`,`file`,`internal_name`)
            VALUES (NULL, ' . (int) $this->id . ' , ' . (int) $id_document . ' , "' .
        pSQL($display_name) . '", "' . pSQL($file_type) .
        '", "' . pSQL($file) . '", "' . pSQL($internal_name) . '")';
        return Db::getInstance()->execute($sql);
    }

    public function addProductImage($id_roja45_quotation_product, $file)
    {
        $quotation_product = new QuotationProduct($id_roja45_quotation_product);
        $quotation_product->custom_image = $file;
        $quotation_product->save();
    }

    public function deleteProductImage($id_roja45_quotation_product)
    {
        $quotation_product = new QuotationProduct($id_roja45_quotation_product);
        unlink(_PS_ROOT_DIR_ . $quotation_product->custom_image);
        $quotation_product->custom_image = '';
        return $quotation_product->save();
    }

    public function deleteDocument($id_roja45_quotation_document)
    {
        $sql =
        'DELETE FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document`
            WHERE id_roja45_quotation_document=' . (int) $id_roja45_quotation_document;
        return Db::getInstance()->execute($sql);
    }

    public function getDocument($id_roja45_quotation_document)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_quotation_document', 'rd');
        $sql->where('rd.id_roja45_quotation=' . (int) $this->id);
        $sql->where('rd.id_roja45_quotation_document=' . (int) $id_roja45_quotation_document);

        if ($document = Db::getInstance()->executeS($sql)) {
            return $document[0];
        }
        return false;
    }
    public function getDocuments()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_quotation_document', 'rd');
        $sql->where('rd.id_roja45_quotation=' . (int) $this->id);

        if ($documents = Db::getInstance()->executeS($sql)) {
            return $documents;
        }
        return array();
    }

    public function getQuotationMessageList()
    {
        return QuotationMessage::getList($this->id);
    }

    public function getQuotationAnswersList($id_lang = null)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        return QuotationAnswer::getQuotationAnswers($id_lang);
    }

    public function addProduct(
        $id_product,
        $id_product_attribute,
        $id_customization,
        $retail_price,
        $qty = 1,
        $comment = null,
        $id_group = null,
        $customizations = array(),
        $id_tax_rules_group = 0,
        $tax_rate = 0,
        $discount = 0,
        $discount_type = 'percentage',
        $ecotax = 0
    ) {
        if (!$id_group) {
            $id_group = Configuration::get('PS_CUSTOMER_GROUP');
        }

        $address = $this->getTaxAddress();
        $defaultCurrency = Currency::getDefaultCurrency();
        $combination = null;
        if (isset($id_product_attribute) && $id_product_attribute) {
            $combination = new Combination($id_product_attribute);
            if (!Validate::isLoadedObject($combination)) {
                throw new Exception('Unable to load combination');
            }
        }
        $product = new Product(
            $id_product,
            false,
            $this->id_lang
        );

        if ($id_quotation_product = QuotationProduct::getQuotationProduct(
            $this->id_roja45_quotation,
            $id_product,
            $id_product_attribute,
            $id_customization
        )) {
            $quotation_product = new QuotationProduct($id_quotation_product);
            $quotation_product->qty = $quotation_product->qty + $qty;
            $quotation_product->discount = $discount;
            $quotation_product->discount_type = $discount_type;
            $quotation_product->date_upd = date('Y-m-d H:i:s');
        } else {
            $counter = (int) $this->getNumberOfProducts();
            $quotation_product = new QuotationProduct();
            $quotation_product->id_roja45_quotation = (int) $this->id;
            $quotation_product->id_shop = (int) $this->id_shop;
            $quotation_product->position = (int) ++$counter;
            $quotation_product->id_lang = (int) $this->id_lang;
            $quotation_product->id_product = (int) $id_product;
            $quotation_product->id_product_attribute = (int) $id_product_attribute;
            $quotation_product->id_customization = (int) $id_customization;
            $quotation_product->product_title = $product->name;
            $quotation_product->custom_price = false;
            $quotation_product->deposit_amount = 100;
            $quotation_product->discount = $discount;
            $quotation_product->discount_type = $discount_type;
            $quotation_product->qty = $qty;
            $quotation_product->id_tax_rules_group = $id_tax_rules_group;
            $quotation_product->tax_rate = $tax_rate;
            $quotation_product->date_add = date('Y-m-d H:i:s');
            $quotation_product->date_upd = date('Y-m-d H:i:s');
        }
        $null = null;
        if ($retail_price) {
            $price_inc = $this->getPriceWithTax(
                $id_product,
                $retail_price,
                Context::getContext(),
                $address
            );

            $price_excl = Tools::ps_round($retail_price, 6);
            $price_incl = Tools::ps_round($price_inc, 6);

            if ($retail_price != (float) $quotation_product->unit_price_tax_excl) {
                $quotation_product->custom_price = true;
            }
        } else {
            $quotation_product->id_product_attribute = null;
            if ($combination) {
                $quotation_product->id_product_attribute = $combination->id;
            }
            $price_excl = Product::priceCalculation(
                $this->id_shop,
                (int) $product->id,
                $quotation_product->id_product_attribute,
                $address->id_country,
                $address->id_state,
                $address->postcode,
                $defaultCurrency->id,
                $id_group,
                $qty,
                false, /* USE TAX*/
                6,
                false,
                true,
                true, /* WITH ECOTAX */
                $null,
                true,
                0,
                true,
                null,
                $qty
            );

            $price_incl = Product::priceCalculation(
                $this->id_shop,
                (int) $product->id,
                $quotation_product->id_product_attribute,
                $address->id_country,
                $address->id_state,
                $address->postcode,
                $defaultCurrency->id,
                $id_group,
                $qty,
                true, /* USE TAX*/
                6,
                false,
                true,
                true, /* WITH ECOTAX */
                $null,
                true,
                0,
                true,
                null,
                $qty
            );
        }

        $quotation_product->unit_price_tax_excl = (float) $price_excl;
        $quotation_product->unit_price_tax_incl = (float) $price_incl;

        $this->total_products += (float) $quotation_product->unit_price_tax_excl;
        $this->total_products_wt += (float) $quotation_product->unit_price_tax_incl;

        $quotation_product->comment = $comment;

        if ($quotation_product->save()) {
            return $quotation_product->id;
        } else {
            throw new Exception(
                'Error adding product: ' .
                $product['id_product_attribute'] . '_' .
                $product['id_product_attribute'] . ' to quotation [' . Db::getInstance()->getMsgError() . ']'
            );
        }
    }

    /**
     * deleteProduct - Update a product line on a quotation.
     *
     * @return json
     *
     */
    public function deleteProduct($quotation_product)
    {
        if (!$quotation_product->delete()) {
            return false;
        }

        if (!$this->save()) {
            return false;
        }

        return true;
    }

    public function resetPrice($id_roja45_quotation_product)
    {
        $this->updateProductPrice($id_roja45_quotation_product, true);
    }

    public function resetCartPrices()
    {
        $products = $this->getQuotationProductList();
        foreach ($products as $product) {
            if ($product['id_specific_price']) {
                $specific_price = new SpecificPrice($product['id_specific_price']);
                $specific_price->delete();
            }
        }

        $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
        foreach ($discounts as $discount) {
            if ($discount['id_cart_rule']) {
                $cart_rule = new CartRule($discount['id_cart_rule']);
                $cart_rule->delete();
            }
        }
    }

    public function resetAllPrices()
    {
        $products = $this->getProducts();
        foreach ($products as $product) {
            $this->resetPrice($product['id_roja45_quotation_product']);
        }
    }

    public function updateProductPrice($id_roja45_quotation_product, $reset = false)
    {
        $product = new QuotationProduct($id_roja45_quotation_product);
        $price_excl = 0;
        $price_incl = 0;
        $with_taxes = false;
        $address = $this->getTaxAddress();
        $id_group = (int) Configuration::get('PS_CUSTOMER_GROUP');

        $precision = Configuration::get('PS_PRICE_DISPLAY_PRECISION');
        if (!$precision) {
            $precision = 2;
        }

        if ($product->custom_price && !$reset) {
            $price = $product->unit_price_tax_excl;
        } else {
            $defaultCurrency = Currency::getDefaultCurrency();
            $null = null;
            $product->custom_price = false;
            $price = Product::priceCalculation(
                $this->id_shop,
                (int) $product->id_product,
                (int) $product->id_product_attribute,
                $address->id_country,
                $address->id_state,
                $address->postcode,
                $defaultCurrency->id,
                $id_group,
                $product->qty,
                false,
                6,
                false,
                true,
                false,
                $null,
                true,
                0,
                true,
                null,
                $product->qty
            );
        }

        /*
        if (Configuration::get('PS_USE_ECOTAX')) {
        $ecotax = $product->ecotax;
        if (isset($product->attribute_ecotax) && $product->attribute_ecotax > 0) {
        $ecotax = $product->attribute_ecotax;
        }
        } else {
        $ecotax = 0;
        }
         */

        $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct(
            (int) $product->id_product,
            Context::getContext()
        );
        $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();

        $price_incl = Tools::ps_round(
            $price + $tax_calculator->getTaxesTotalAmount(
                $price
            ),
            (int) $precision
        );
        $price_excl = Tools::ps_round($price, (int) $precision);
        /*$product->unit_price_tax_excl = $price_excl + $ecotax;
        $product->unit_price_tax_incl = $price_incl + $ecotax;*/
        $product->unit_price_tax_excl = $price_excl;
        $product->unit_price_tax_incl = $price_incl;
        $product->tax_rate = $tax_calculator->getTotalRate();
        $product->update();
    }

    public function updateAllPrices()
    {
        $products = $this->getProducts();

        foreach ($products as $product) {
            $this->updateProductPrice($product['id_roja45_quotation_product'], false);
        }
    }

    public function getQuotationTotal($with_taxes = true, $type = self::BOTH)
    {
        $context = Context::getContext();
        // TODO - add dirty flag, if false return cached values.  set to true when changes to getSumm made.
        $type = (int) $type;

        $this->quotation_total = 0;
        $this->quotation_total_charges = 0;
        $this->quotation_total_shipping = 0;
        $this->quotation_total_discount = 0;
        $this->quotation_total_handling = 0;

        $products_total = array();

        $ecotax_total = 0;
        $total_charges = 0;
        $total_charges_wt = 0;
        $total_shipping_exc = 0;
        $total_shipping_inc = 0;
        $total_handling = 0;
        $total_handling_wt = 0;

        //$id_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
        $products = $this->getProducts();

        $address = $this->getTaxAddress();
        if ($type == self::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = self::ONLY_PRODUCTS;
        }

        $precision = Configuration::get('PS_PRICE_DISPLAY_PRECISION');
        if (!$precision) {
            $precision = 2;
        }

        foreach ($products as $product) {
            // products refer to the cart details
            if ($with_taxes) {
                $price = (float) $product['unit_price_tax_incl'];
            } else {
                $price = (float) $product['unit_price_tax_excl'];
            }

            // TODO -for each key 0 discount, apply it
            // for each id_product, appy it.

            if (Configuration::get('PS_USE_ECOTAX')) {
                $ecotax = $product['ecotax'];
                if (isset($product['attribute_ecotax']) && $product['attribute_ecotax'] > 0) {
                    $ecotax = $product['attribute_ecotax'];
                }
            } else {
                $ecotax = 0;
            }

            if ($with_taxes) {
                $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int) $product['id_product'], $context);
                if ($ecotax != 0) {
                    $ecotax_tax_calculator = TaxManagerFactory::getManager(
                        $address,
                        (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID')
                    )->getTaxCalculator();
                }
            } else {
                $id_tax_rules_group = 0;
            }

            if (!isset($products_total[$id_tax_rules_group])) {
                $products_total[$id_tax_rules_group] = 0;
            }

            $products_total[$id_tax_rules_group] += (Tools::ps_round(
                ($price * (int) $product['qty']),
                (int) $precision
            ));

            if ($ecotax != 0) {
                $ecotax_price = $ecotax * (int) $product['qty'];

                if ($with_taxes) {
                    $ecotax_total += ($ecotax_price * (1 + Tax::getProductEcotaxRate() / 100));
                } else {
                    $ecotax_total += $ecotax_price;
                }
            }
        }

        foreach ($products_total as $price) {
            $this->quotation_total += $price;
        }

        if (!in_array($type, array(self::ONLY_PRODUCTS))) {
            // TODO - If apply discounts after charges and shipping, apply here.
            $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
            $total_discounts = $this->calculateDiscount($this->quotation_total, $discounts, $with_taxes);
            if ($type == self::ONLY_DISCOUNTS) {
                return $total_discounts;
            }

            if ($type == self::TOTAL_BEFORE_DISCOUNT) {
                return $this->quotation_total;
            }

            $this->quotation_total -= $total_discounts;
            $this->quotation_total_discount -= $total_discounts;

            if ($type == self::TOTAL_AFTER_DISCOUNT) {
                return $this->quotation_total;
            }

            $this->quotation_total_products = $this->quotation_total;

            // calculate charges
            $charges = $this->getQuotationChargeList(QuotationCharge::$CHARGE);
            foreach ($charges as $charge) {
                $total_charges += $charge['charge_amount'];
                $total_charges_wt += $charge['charge_amount_wt'];
            }

            $charges = $this->getQuotationChargeList(QuotationCharge::$SHIPPING);
            foreach ($charges as $charge) {
                if (isset($charge['charge_default']) && $charge['charge_default']) {
                    $total_shipping_exc += $charge['charge_amount'];
                    $total_shipping_inc += $charge['charge_amount_wt'];
                    $total_handling += $charge['charge_handling'];
                    $total_handling_wt += $charge['charge_handling_wt'];
                }
            }

            // TODO - If apply discounts after charges and shipping, apply here.
            if ($with_taxes) {
                $this->quotation_total += $total_charges_wt;
                $this->quotation_total += $total_shipping_inc;
                $this->quotation_total += $total_handling_wt;
                $this->quotation_total_charges += $total_charges_wt;
                $this->quotation_total_shipping += $total_shipping_inc;
                $this->quotation_total_handling += $total_handling_wt;
            } else {
                $this->quotation_total += $total_charges;
                $this->quotation_total += $total_shipping_exc;
                $this->quotation_total += $total_handling;
                $this->quotation_total_charges += $total_charges;
                $this->quotation_total_shipping += $total_shipping_exc;
                $this->quotation_total_handling += $total_handling;
            }
        }

        if ($this->quotation_total < 0 && $type != self::ONLY_DISCOUNTS) {
            return 0;
        }

        if ($type == self::ONLY_SHIPPING) {
            return $this->quotation_total_shipping;
        }

        if ($type == self::ONLY_HANDLING) {
            return $this->quotation_total_handling;
        }

        if ($type == self::ONLY_CHARGES) {
            return $this->quotation_total_charges;
        }

        if ($type == self::ONLY_DISCOUNTS) {
            return $this->quotation_total_discount;
        }

        $this->is_dirty = false;
        return (float) $this->quotation_total;
    }

    public function getQuotationTotals($with_taxes = true, $type = self::BOTH, $include_additional_shipping = false)
    {
        $totals = array(
            'quotation_total_discounts' => 0,
            'quotation_total_before_discount' => 0,
            'quotation_total_after_discount' => 0,
            'quotation_total_products' => 0,
            'quotation_total_shipping' => 0,
            'quotation_total_additional_shipping' => 0,
            'quotation_total_handling' => 0,
            'quotation_total_charges' => 0,
            'quotation_total_discount' => 0,
            'quotation_total_ecotax' => 0,
            'quotation_total_customizations' => 0,
            'quotation_total' => 0,
        );

        $context = Context::getContext();
        // TODO - add dirty flag, if false return cached values.  set to true when changes to getSumm made.
        $type = (int) $type;

        $quotation_total = 0;
        $quotation_total_charges = 0;
        $quotation_total_shipping = 0;
        $quotation_total_additional_shipping = 0;
        $quotation_total_discount = 0;
        $quotation_total_handling = 0;

        $products_total = array();

        $ecotax_total = 0;
        $ecotax_total_wt = 0;
        $customization_total = 0;
        $customization_total_wt = 0;
        $total_charges = 0;
        $total_charges_wt = 0;
        $total_shipping_exc = 0;
        $total_shipping_inc = 0;
        $total_shipping_additional_exc = 0;
        $total_shipping_additional_inc = 0;
        $total_handling = 0;
        $total_handling_wt = 0;

        //$id_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
        $products = $this->getProducts();

        $address = $this->getTaxAddress();
        if ($type == self::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = self::ONLY_PRODUCTS;
        }

        $precision = Configuration::get('PS_PRICE_DISPLAY_PRECISION');
        if (!$precision) {
            $precision = 2;
        }

        $id_tax_rules_group = 0;
        $currency = new Currency($this->id_currency);
        foreach ($products as $product) {
            // products refer to the cart details
            if ($with_taxes) {
                $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int) $product['id_product'], $context);
                $price = (float) $product['unit_price_tax_incl'];
            } else {
                $price = (float) $product['unit_price_tax_excl'];
            }

            $price = Tools::convertPrice(
                $price,
                $currency,
                true
            );
            /*$price = Tools::ps_round(
                Tools::convertPrice(
                    $price,
                    $currency,
                    true
                ),
                6
            );*/

            if (Configuration::get('PS_USE_ECOTAX')) {
                $ecotax = $product['ecotax'];
                if (isset($product['attribute_ecotax']) && $product['attribute_ecotax'] > 0) {
                    $ecotax = $product['attribute_ecotax'];
                }
                $ecotax_tax_calculator = TaxManagerFactory::getManager(
                    $address,
                    (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID')
                )->getTaxCalculator();
            } else {
                $ecotax = 0;
            }

            if (!isset($products_total[$id_tax_rules_group])) {
                $products_total[$id_tax_rules_group] = 0;
            }

            $customization_price = 0;
            if ($product['customization_cost_exc'] > 0) {
                if ($with_taxes) {
                    $customization_price = $product['customization_cost_inc'];
                    if ($product['customization_cost_type'] == 2) {
                        $customization_price = $product['customization_cost_inc'] * (int) $product['qty'];
                    }
                    $product['customization_price'] = $customization_price;
                } else {
                    $customization_price = $product['customization_cost_exc'];
                    if ($product['customization_cost_type'] == 2) {
                        $customization_price = $product['customization_cost_exc'] * (int) $product['qty'];
                    }
                    $product['customization_price'] = $customization_price;
                }
                $customization_total += $customization_price;
            }
            if ($ecotax != 0) {
                $ecotax_price = $ecotax * (int) $product['qty'];

                $product['ecotax'] = $ecotax;
                if ($with_taxes) {
                    $product['ecotax'] = $product['ecotax'] * (1 + Tax::getProductEcotaxRate() / 100);
                    $ecotax_total += $product['ecotax'];
                } else {
                    $ecotax_total += $ecotax_price;
                }
            }

            $products_total[$id_tax_rules_group] += Tools::ps_round(
                //($price * (int) $product['qty']) + $customization_price + $product['ecotax'],
                ($price * (int) $product['qty']) + $customization_price,
                6
            );
            $total_shipping_additional_exc += $product['additional_shipping_cost'];
            if ($with_taxes) {
                $total_shipping_additional_inc += $this->getPriceWithTax(
                    $product['id_product'],
                    $product['additional_shipping_cost'],
                    $context
                );
            }
        }

        foreach ($products_total as $price) {
            $quotation_total += $price;
            /*$quotation_total += Tools::ps_round(
                $price,
                2
            );*/
        }
        $quotation_total = Tools::convertPrice(
            $quotation_total,
            $currency,
            false
        );

        // TODO - If apply discounts after charges and shipping, apply here.
        $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
        $total_discounts = $this->calculateDiscount(
            $quotation_total,
            $discounts,
            $with_taxes
        );

        /**
         * This must be wrong, but there seems to be a rounding applied when prestashop calculcates a discount for the cart.
         */
        /*$total_discounts = Tools::ps_round(
            $total_discounts,
            2
        );*/

        $totals['quotation_total_discounts'] = (float) $total_discounts;
        $totals['quotation_total_before_discount'] = (float) $quotation_total;
        $quotation_total -= $total_discounts;
        $quotation_total_discount += $total_discounts;

        $totals['quotation_total_after_discount'] = (float) $quotation_total;
        $totals['quotation_total_customizations'] = (float) $customization_total;
        $totals['quotation_total_ecotax'] = (float) $ecotax_total;

        $quotation_total_products = $quotation_total;
        $totals['quotation_total_products'] = (float) $quotation_total_products;
        $charges = $this->getQuotationChargeList(QuotationCharge::$SHIPPING);
        foreach ($charges as $charge) {
            if ($charge['charge_default']) {
                $total_shipping_exc += $charge['charge_amount'];
                $total_shipping_inc += $charge['charge_amount_wt'];
                $total_handling += $charge['charge_handling'];
                $total_handling_wt += $charge['charge_handling_wt'];
            }
        }

        /*
        $charges = $this->getQuotationChargeList(QuotationCharge::$HANDLING);
        foreach ($charges as $charge) {
        $total_handling += $charge['charge_amount'];
        $total_handling_wt += $charge['charge_amount_wt'];
        }
         */
        // TODO - If apply discounts after charges and shipping, apply here.
        if ($with_taxes) {
            $quotation_total += $total_charges_wt;
            $quotation_total += $total_shipping_inc;
            $quotation_total += $total_handling_wt;
            $quotation_total_charges += $total_charges_wt;
            $quotation_total_shipping += $total_shipping_inc;
            $quotation_total_additional_shipping += $total_shipping_additional_inc;
            $quotation_total_handling += $total_handling_wt;
        } else {
            $quotation_total += $total_charges;
            $quotation_total += $total_shipping_exc;
            $quotation_total += $total_handling;
            $quotation_total_charges += $total_charges;
            $quotation_total_shipping += $total_shipping_exc;
            $quotation_total_additional_shipping += $total_shipping_additional_exc;
            $quotation_total_handling += $total_handling;
        }
        $totals['quotation_total_shipping'] = (float) $quotation_total_shipping;
        $totals['quotation_total_additional_shipping'] = (float) $quotation_total_additional_shipping;
        $totals['quotation_total_handling'] = (float) $quotation_total_handling;
        $totals['quotation_total_charges'] = (float) $quotation_total_charges;
        $totals['quotation_total_discount'] = (float) $quotation_total_discount;
        $totals['quotation_total'] = (float) $quotation_total;

        return $totals;
    }

    private function calculateDiscount($quotation_total, $discounts, $use_tax = true)
    {
        $current_total = 0;
        foreach ($discounts as $discount) {
            // If the cart rule offers a reduction, the amount is prorated (with the products in the package)
            if ($discount['charge_method'] == QuotationCharge::$PERCENTAGE) {
                $current_total += $quotation_total * ((float) $discount['charge_value'] / 100);
            } elseif ($discount['charge_method'] == QuotationCharge::$VALUE) {
                if ($use_tax) {
                    $current_total += (float) $discount['charge_amount_wt'];
                } else {
                    $current_total += (float) $discount['charge_amount'];
                }
            }
        }
        return $current_total;
    }

    public function getTotalShippingCost($use_tax = true)
    {
        $shipping = $this->getQuotationTotal($use_tax, RojaQuotation::ONLY_SHIPPING);
        if (Configuration::get(
            'ROJA45_QUOTATIONSPRO_INCLUDEHANDLING'
        )) {
            $handling = $this->getQuotationTotal($use_tax, RojaQuotation::ONLY_HANDLING);
            $shipping += $handling;
        }

        return $shipping;
    }

    public function getSummaryDetails($id_lang = null, $id_currency = null, $show_taxes = true, $hide_prices = false)
    {
        $context = Context::getContext();
        if (!$id_lang) {
            $id_lang = $context->language->id;
        }

        if (!$id_currency) {
            $id_currency = $this->id_currency;
        }
        $currency = new Currency($id_currency);
        $status = new QuotationStatus($this->id_roja45_quotation_status, $id_lang);
        $language = new Language($id_lang);
        $products = $this->getProducts();
        $field_address_invoice = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE');
        $field_address_delivery = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY');

        if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING')) {
            if ($this->id_customer) {
                $show_taxes = !Group::getPriceDisplayMethod(Customer::getDefaultGroupId($this->id_customer));
            }
        }

        $has_customizations = false;
        $has_additional_shipping = false;
        $has_discounts = false;
        $has_comments = false;
        $total_ecotax = 0;
        $total_ecotax_inc = 0;
        $total_customizations = 0;
        $total_customizations_inc = 0;
        $tax_rate_summary = array();
        $values_by_tax = array();
        foreach ($products as &$product) {
            $total_product_customizations = 0;
            $total_product_customizations_inc = 0;
            $total_additional_shipping = 0;
            $tax_group_id = 0;
            $tax_group_name = Module::getInstanceByName(
                'roja45quotationspro'
            )->l('No Tax');

            if (!array_key_exists($product['id_tax_rules_group'], $values_by_tax)) {
                if ($product['id_tax_rules_group']) {
                    $tax_group = new TaxRulesGroup(
                        $product['id_tax_rules_group'],
                        $id_lang
                    );
                    $tax_group_id = $tax_group->id;
                    $tax_group_name = $tax_group->name;
                }

                $values_by_tax[$product['id_tax_rules_group']] = [
                    'tax_summary_name' => $tax_group_name,
                    'tax_summary_total_exc' => 0,
                    'tax_summary_total_inc' => 0,
                    'tax_summary_total_tax' => 0,
                    'tax_summary_tax_rate' => 0,
                    'tax_rate' => $product['tax_rate'],
                    'tax_rate_formatted' => (string) ((int) $product['tax_rate']) . '%',
                ];
            }

            if (!array_key_exists($product['id_tax_rules_group'], $tax_rate_summary)) {
                $tax_rate_summary[$product['id_tax_rules_group']] = [
                    'tax_summary_name' => $tax_group_name,
                    'tax_rate' => $product['tax_rate'],
                    'tax_rate_formatted' => (string) ((int) $product['tax_rate']) . '%',
                ];
            }
            if (isset($product['customizations']) && count($product['customizations'])) {
                $has_customizations = true;
            }

            if (isset($product['discount']) && $product['discount']) {
                $has_discounts = true;
            }

            if (isset($product['comment']) && $product['comment']) {
                $has_comments = true;
            }

            if (isset($product['additional_shipping_cost']) && $product['additional_shipping_cost']>0) {
                $has_additional_shipping = true;
                $total_additional_shipping += $product['additional_shipping_cost'];
            }
            $product['admin_link'] = '';
            if (isset($context->employee)) {
                $product['admin_link'] = $context->link->getAdminLink(
                    'AdminProducts',
                    true,
                    array(
                        'updateproduct' => 1,
                        'id_product' => (int) $product['id_product'],
                    ),
                    array(
                        'updateproduct' => 1,
                        'id_product' => (int) $product['id_product'],
                    )
                );
            }

            if (!isset($product['discount'])) {
                $product['discount'] = 0;
            }
            $product['discount'] = RojaFortyFiveQuotationsProCore::displayNumber(
                $product['discount'],
                $currency
            );
            $product['product_discount'] = $product['discount'];
            $product['product_discount_prefix'] = '';
            $product['product_discount_postfix'] = '';
            if ($product['discount_type'] == 'percentage') {
                $product['product_discount_postfix'] = '%';
            } else {
                $product['product_discount_prefix'] = RojaFortyFiveQuotationsProCore::getCurrencySymbol($currency);
            }

            $product_list_price = $show_taxes ? $product['list_price_incl'] : $product['list_price_excl'];
            $product_subtotal = $show_taxes ? $product['product_price_subtotal_incl'] : $product['product_price_subtotal_excl'];

            if ($hide_prices && !$product_list_price) {
                $text = Module::getInstanceByName(
                    'roja45quotationspro'
                )->l('Price Requested', 'QuotationsProFront');
                $product['product_list_price'] = $text;
                $product['product_list_price_inc'] = $text;
                $product['product_list_price_exc'] = $text;
                $product['product_subtotal'] = $text;
                $product['product_subtotal_exc'] = $text;
                $product['product_subtotal_inc'] = $text;
            } else {
                $product['product_list_price'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product_list_price,
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_list_price_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['list_price_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_list_price_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['list_price_excl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_subtotal'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product_subtotal,
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_subtotal_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_subtotal_excl'],
                        $currency,
                        true
                    ),
                    $currency
                );

                $product['product_subtotal_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['product_price_subtotal_incl'],
                        $currency,
                        true
                    ),
                    $currency
                );
            }

            if ($product['customization_cost_type'] == 1) {
                $total_product_customizations += $product['customization_cost_exc'];
                $total_product_customizations_inc += $product['customization_cost_inc'];
            } else {
                $total_product_customizations += $product['customization_cost_exc'] * $product['qty'];
                $total_product_customizations_inc += $product['customization_cost_inc'] * $product['qty'];
            }

            if ($product['customization_cost_exc']) {
                $product['product_customization_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['customization_cost_exc'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_customization_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $product['customization_cost_inc'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_customization'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $show_taxes ?
                        $product['customization_cost_inc'] : $product['customization_cost_exc'],
                        $currency,
                        true
                    ),
                    $currency
                );
            } else {
                $product['customization_cost_exc'] = '';
                $product['customization_cost_inc'] = '';
                $product['product_customization'] = '';
            }

            $total_customizations += $total_product_customizations;
            $total_customizations_inc += $total_product_customizations_inc;

            if ($total_product_customizations) {
                $product['product_customization_total_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $total_product_customizations,
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_customization_total_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $total_product_customizations_inc,
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_customization_total'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $show_taxes ?
                        $total_product_customizations_inc : $total_product_customizations,
                        $currency,
                        true
                    ),
                    $currency
                );
            } else {
                $product['product_customization_total_exc'] = '';
                $product['product_customization_total_inc'] = '';
                $product['product_customization_total'] = '';
            }

            $product['product_customization_inc_currency'] = Tools::convertPrice(
                (float) $product['customization_cost_exc'],
                $currency,
                true
            );
            $product['product_customization_exc_currency'] = Tools::convertPrice(
                (float) $product['customization_cost_inc'],
                $currency,
                true
            );

            $total_ecotax += $product['ecotax'];
            $total_ecotax_inc += $product['ecotax_inc'];
            $product['product_ecotax_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $product['ecotax'],
                    $currency,
                    true
                ),
                $currency
            );
            $product['product_ecotax_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $product['ecotax_inc'],
                    $currency,
                    true
                ),
                $currency
            );
            $product['product_ecotax_currency'] = Tools::convertPrice(
                $product['ecotax'],
                $currency,
                true
            );
            $product['product_ecotax_inc_currency'] = Tools::convertPrice(
                $product['ecotax_inc'],
                $currency,
                true
            );

            $product['product_ecotax'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ?
                    $product['product_ecotax_inc_currency'] : $product['product_ecotax_currency'],
                    $currency,
                    true
                ),
                $currency
            );

            $product['product_ecotax_total_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $product['ecotax'] * (int) $product['qty'],
                    $currency,
                    true
                ),
                $currency
            );
            $product['product_ecotax_total_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $product['ecotax_inc'] * (int) $product['qty'],
                    $currency,
                    true
                ),
                $currency
            );

            $product['product_ecotax_total'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ?
                    ($product['ecotax_inc'] * (int) $product['qty']) : ($product['ecotax'] * (int) $product['qty']),
                    $currency,
                    true
                ),
                $currency
            );

            $product['product_tax'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $product['tax_paid'],
                    $currency,
                    true
                ),
                $currency
            );
            $product['additional_shipping'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $product['additional_shipping_cost'],
                    $currency,
                    true
                ),
                $currency
            );

            $product_unit_price = $show_taxes ? $product['unit_price_tax_incl'] : $product['unit_price_tax_excl'];

            $product['unit_price_tax_excl_currency'] = Tools::ps_round(
                Tools::convertPrice(
                    $product['unit_price_tax_excl'],
                    $currency,
                    true
                ),
                6
            );
            $product['unit_price_tax_incl_currency'] = Tools::ps_round(
                Tools::convertPrice(
                    $product['unit_price_tax_incl'],
                    $currency,
                    true
                ),
                6
            );

            $product['product_unit_price'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::ps_round(
                    Tools::convertPrice(
                        $product_unit_price,
                        $currency,
                        true
                    ),
                    6
                ),
                $currency
            );

            $product['product_price_currency'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::ps_round(
                    Tools::convertPrice(
                        $product_unit_price,
                        $currency,
                        true
                    ),
                    6
                ),
                $currency
            );

            $product['unit_price_tax_excl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::ps_round(
                    Tools::convertPrice(
                        $product['unit_price_tax_excl'],
                        $currency,
                        true
                    ),
                    6
                ),
                $currency
            );
            $product['unit_price_tax_incl_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::ps_round(
                    Tools::convertPrice(
                        $product['unit_price_tax_incl'],
                        $currency,
                        true
                    ),
                    6
                ),
                $currency
            );

            $product['product_price_subtotal_excl_currency'] =
                Tools::ps_round(
                    Tools::convertPrice(
                        $product['product_price_subtotal_excl'],
                        $currency,
                        true
                    ),
                    6
                );

            $product['product_price_subtotal_incl_currency'] =
                Tools::ps_round(
                    Tools::convertPrice(
                        $product['product_price_subtotal_incl'],
                        $currency,
                        true
                    ),
                    6
                );

            $values_by_tax[$product['id_tax_rules_group']]['tax_summary_total_exc'] += $product['product_price_subtotal_excl'];
            $values_by_tax[$product['id_tax_rules_group']]['tax_summary_total_inc'] += $product['product_price_subtotal_incl'];
            $values_by_tax[$product['id_tax_rules_group']]['tax_summary_total_tax'] += $product['tax_paid'];

            if (isset($product['customizations']) && count($product['customizations'])) {
                foreach ($product['customizations'] as &$customization) {
                    if ($customization['type']) {
                        $formatted = $customization['name'] . ' : '. $customization['value'];
                    } else {
                        $link = $context->link->getMediaLink(_THEME_PROD_PIC_DIR_ . $customization['value']);

                        $formatted = '<p>'.$customization['name'].'</p><img class="img-thumbnail" src="'. $link . '.jpg" alt="" />';
                    }
                    $customization['formatted'] = $formatted;
                }
            }
        }

        ksort($values_by_tax);
        foreach ($values_by_tax as $key => &$value_by_tax) {
            $value_by_tax['tax_summary_total_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $value_by_tax['tax_summary_total_exc'],
                    $currency,
                    true
                ),
                $currency
            );
            $value_by_tax['tax_summary_total_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $value_by_tax['tax_summary_total_inc'],
                    $currency,
                    true
                ),
                $currency
            );
            $value_by_tax['tax_summary_total_tax'] = RojaFortyFiveQuotationsProCore::formatPrice(
                $value_by_tax['tax_summary_total_tax'],
                $currency
            );
        }

        $sales_person = '';
        if ($this->id_employee) {
            $employee = new Employee($this->id_employee);
            $sales_person = $employee->firstname . ' ' . $employee->lastname;
        }

        $today = new DateTime();
        $quotation_date_created = new DateTime($this->date_add);
        $quotation_date_updated = new DateTime($this->date_upd);
        $quotation_data = array(
            'quotation_id' => $this->id,
            'quotation_status_id' => $status->id,
            'quotation_status_status' => $status->status,
            'quotation_status_code' => $status->code,
            'quotation_name' => (!empty($this->quote_name)) ? $this->quote_name : '',
            'quotation_reference' => $this->reference,
            'date' => $today->format($context->language->date_format_lite),
            'date_full' => $today->format($context->language->date_format_full),
            'quotation_date_created' => $quotation_date_created->format($context->language->date_format_full),
            'quotation_date_updated' => $quotation_date_updated->format($context->language->date_format_full),
            'quotation_sales_person' => $sales_person,
            'quotation_has_customizations' => $has_customizations,
            'quotation_has_additional_shipping' => $has_additional_shipping,
            'quotation_has_ecotax' => ($total_ecotax > 0) ? true : false,
            'quotation_has_customization_cost' => ($total_customizations > 0) ? true : false,
            'quotation_has_discounts' => $has_discounts,
            'quotation_has_comments' => $has_comments,
            'quotation_products' => array_values($products),
            'language_id' => $language->id,
            'language_name' => $language->name,
            'language_iso_code' => $language->iso_code,
            'currency_id' => $currency->id,
            'currency_name' => $currency->name,
            'currency_iso_code' => $currency->iso_code,
            'currency_symbol' => RojaFortyFiveQuotationsProCore::getCurrencySymbol($currency),
            'show_taxes' => $show_taxes,
            'show_prices' => Configuration::get('ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY'),
            'show_product_customizations' => $has_customizations,
            'enable_customization_cost' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST'
            ),
            'enable_customization_cost_type' => Configuration::get(
                'ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE'
            ),
            'use_taxes' => (int) $this->calculate_taxes,
            'tax_text' => ((int) $show_taxes) ?
                Module::getInstanceByName('roja45quotationspro')->l('inc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)) :
                Module::getInstanceByName('roja45quotationspro')->l('exc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)),
            'tax_inc_text' => Module::getInstanceByName('roja45quotationspro')->l('inc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)),
            'tax_exc_text' => Module::getInstanceByName('roja45quotationspro')->l('exc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)),
            'quotation_totals_by_tax' => $values_by_tax,
            'quotation_tax_summary' => $tax_rate_summary,
        );

        $requestJSON = false;
        $request = array();
        $quotation_request = new QuotationRequest($this->id_request);
        $form_data = array();
        if ($quotation_request->form_data) {
            if ($requestJSON = json_decode($quotation_request->form_data)) {
                $request = array();
                $counter = 0;
                foreach ($requestJSON->columns as $column) {
                    foreach ($column->fields as $field) {
                        if (($field->name != 'FIRSTNAME') &&
                            ($field->name != 'LASTNAME') &&
                            ($field->name != 'CONTACT_EMAIL')
                        ) {
                            $request[$counter]['name'] = $field->name;
                            $request[$counter]['value'] = isset($field->value) ? $field->value : '';
                            $request[$counter]['label'] = $field->label;
                            if (isset($field->type) && ($field->type == 'CUSTOM_SELECT')) {
                            } elseif (isset($field->type) && ($field->type == 'SHIPPING_METHOD') && isset($field->value)) {
                                $carrier = new Carrier($field->value, $context->language->id);
                                $request[$counter]['value'] = $carrier->name;
                                $shipping_method = $carrier->name;
                            } elseif (isset($field->type) && ($field->type == 'COUNTRY') && isset($field->value)) {
                                $country = new Country($field->value, $context->language->id);
                                $request[$counter]['value'] = $country->name;
                            } elseif (isset($field->type) && ($field->type == 'STATE') && isset($field->value)) {
                                $state = new State($field->value, $context->language->id);
                                $request[$counter]['value'] = $state->name;
                            } elseif (isset($field->type) && ($field->type == 'ADDRESS_SELECTOR')) {
                                if ($field->name == $field_address_invoice) {
                                    $address = new Address($this->id_address_invoice);
                                } else if ($field->name == $field_address_delivery) {
                                    $address = new Address($this->id_address_delivery);
                                }

                                if (!empty($address->id)) {
                                    $address = AddressFormat::generateAddress(
                                        $address,
                                        array(),
                                        ', ',
                                        ' ',
                                        array()
                                    );
                                    $field->value = $address;
                                    $request[$counter]['value'] = $address;
                                }
                            }
                            ++$counter;
                            $token = strtolower($field->name);
                            $form_data[$token] = isset($field->value) ? $field->value : '';
                        }
                    }
                }
            }
        } else {
            if ($requestJSON = json_decode($this->form_data)) {
                foreach ($requestJSON as $key => $field) {
                    if (($field->name != 'FIRSTNAME') &&
                        ($field->name != 'LASTNAME') &&
                        ($field->name != 'CONTACT_EMAIL')
                    ) {
                        $request[$key]['name'] = $field->name;
                        $request[$key]['value'] = isset($field->value) ? $field->value : '';
                        $request[$key]['label'] = $field->label;
                        if (isset($field->type) && ($field->type == 'CUSTOM_SELECT')) {
                        } elseif (isset($field->type) && ($field->type == 'SHIPPING_METHOD')) {
                            $carrier = new Carrier($field->value, $context->language->id);
                            $request[$key]['value'] = $carrier->name;
                            $shipping_method = $carrier->name;
                        } elseif (isset($field->type) && ($field->type == 'COUNTRY')) {
                            $country = new Country($field->value, $context->language->id);
                            $request[$key]['value'] = $country->name;
                        } elseif (isset($field->type) && ($field->type == 'STATE')) {
                            $state = new State($field->value, $context->language->id);
                            $request[$key]['value'] = $state->name;
                        }
                        $token = strtolower($field->name);
                        $form_data[$token] = $field->value;
                    }
                }
            }
        }
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $quotation_data = array_merge(
                $quotation_data,
                array(
                    'request_data' => $requestJSON,
                )
            );
            $quotation_data = array_merge(
                $quotation_data,
                $form_data
            );
        }
        $quotation_data = array_merge(
            $quotation_data,
            array(
                'form_data' => $request,
            )
        );

        if ($this->id_customer) {
            $customer = new Customer($this->id_customer);
            $customer_title = '';
            if ($customer->id_gender) {
                $customer_title = new Gender($customer->id_gender, $context->language->id);
                $customer_title = $customer_title->name;
            }

            $customer_address = $this->getTaxAddress();
            $customer_data = array(
                'customer_id' => $this->id_customer,
                'customer_title' => $customer_title,
                'customer_firstname' => $this->firstname,
                'customer_lastname' => $this->lastname,
                'customer_email' => $this->email,
                'customer_address_id' => isset($customer_address->id) ? $customer_address->id : 0,
                'customer_address_address1' => !empty($customer_address->address1) ? $customer_address->address1 : '',
                'customer_address_address2' => !empty($customer_address->address2) ? $customer_address->address2 : '',
                'customer_address_city' => !empty($customer_address->city) ? $customer_address->city : '',
                'customer_address_postcode' => !empty($customer_address->postcode) ? $customer_address->postcode : '',
                'customer_address_country' => !empty($customer_address->country) ? $customer_address->country : '',
                'customer_company' => !empty($customer_address->company) ? $customer_address->company : '',
                'customer_phone' => !empty($customer_address->phone) ? $customer_address->phone : '',
                'customer_mobile' => !empty($customer_address->phone_mobile) ? $customer_address->phone_mobile : '',
                'customer_dni' => !empty($customer_address->dni) ? $customer_address->dni : '',
                'customer_vat_number' => !empty($customer_address->vat_number) ? $customer_address->vat_number : '',
                'customer_account_link' => $context->link->getPageLink(
                    'my-account',
                    true,
                    $context->language->id,
                    null,
                    false,
                    $context->shop->id
                ),
                'customer_account_quotation_link' => $context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'action' => 'getQuotationDetails',
                        'id_roja45_quotation' => $this->id,
                    ),
                    true
                ),
                'quotation_purchase_link' => $context->link->getModuleLink(
                    'roja45quotationspro',
                    'QuotationsProFront',
                    array(
                        'p' => $this->id,
                    ),
                    true
                ),
            );

            $invoice_address = null;
            $delivery_address = null;
            if (isset($this->id_address_invoice)) {
                $invoice_address = new Address($this->id_address_invoice, $context->language->id);
                $invoice_data = array(
                    'customer_invoice_firstname' => $invoice_address->firstname,
                    'customer_invoice_lastname' => $invoice_address->lastname,
                    'customer_invoice_address_id' => isset($invoice_address->id) ? $invoice_address->id : 0,
                    'customer_invoice_address_address1' => !empty($invoice_address->address1) ? $invoice_address->address1 : '',
                    'customer_invoice_address_address2' => !empty($invoice_address->address2) ? $invoice_address->address2 : '',
                    'customer_invoice_address_city' => !empty($invoice_address->city) ? $invoice_address->city : '',
                    'customer_invoice_address_postcode' => !empty($invoice_address->postcode) ? $invoice_address->postcode : '',
                    'customer_invoice_address_country' => !empty($invoice_address->country) ? $invoice_address->country : '',
                    'customer_invoice_phone' => !empty($invoice_address->phone) ? $invoice_address->phone : '',
                    'customer_invoice_mobile' => !empty($invoice_address->phone_mobile) ? $invoice_address->phone_mobile : '',
                );
                $customer_data = array_merge(
                    $invoice_data,
                    $customer_data
                );
            }
            if (isset($this->id_address_delivery)) {
                $delivery_address = new Address($this->id_address_delivery, $context->language->id);
                $delivery_data = array(
                    'customer_delivery_firstname' => $delivery_address->firstname,
                    'customer_delivery_lastname' => $delivery_address->lastname,
                    'customer_delivery_address_id' => isset($delivery_address->id) ? $delivery_address->id : 0,
                    'customer_delivery_address_address1' => !empty($delivery_address->address1) ? $delivery_address->address1 : '',
                    'customer_delivery_address_address2' => !empty($delivery_address->address2) ? $delivery_address->address2 : '',
                    'customer_delivery_address_city' => !empty($delivery_address->city) ? $delivery_address->city : '',
                    'customer_delivery_address_postcode' => !empty($delivery_address->postcode) ? $delivery_address->postcode : '',
                    'customer_delivery_address_country' => !empty($delivery_address->country) ? $delivery_address->country : '',
                    'customer_delivery_phone' => !empty($delivery_address->phone) ? $delivery_address->phone : '',
                    'customer_delivery_mobile' => !empty($delivery_address->phone_mobile) ? $delivery_address->phone_mobile : '',
                );
                $customer_data = array_merge(
                    $delivery_data,
                    $customer_data
                );
            }
        } else {
            $customer_data = array(
                'customer_title' => '',
                'customer_firstname' => $this->firstname,
                'customer_lastname' => $this->lastname,
                'customer_email' => $this->email,
                'customer_phone' => '',
            );
        }

        $quotation_request = new QuotationRequest($this->id_request);
        if ($quotation_request->form_data) {
            $requestJSON = json_decode($quotation_request->form_data);
        } else {
            $requestJSON = json_decode($this->form_data);
        }

        $template_path = RojaFortyFiveQuotationsProCore::getEditorTemplatePath('customer_request_form');
        $tpl = $context->smarty->createTemplate(
            $template_path
        );

        $tpl->assign([
            'request_data' => $requestJSON
        ]);
        $customer_form = $tpl->fetch();

        $customer_form_txt = \Soundasleep\Html2Text::convert(
            $customer_form,
            [
                'ignore_errors' => true
            ]
        );

        $customer_data['customer_form'] = $customer_form;
        $customer_data['customer_form_text'] = $customer_form_txt;

        $quotation_data = array_merge(
            $quotation_data,
            $customer_data
        );

        $defaultCurrency = Currency::getDefaultCurrency();

        if ($this->expiry_date != '0000-00-00 00:00:00') {
            $date = new DateTime($this->expiry_date);
            $expiry = array(
                'quotation_expiry_date' => $date->format($context->language->date_format_lite),
                'quotation_expiry_time' => $date->format('H:i'),
            );
            $quotation_data = array_merge(
                $quotation_data,
                $expiry
            );
        }

        $totals_inc = $this->getQuotationTotals(true);
        $totals_exc = $this->getQuotationTotals(false);

        /*$base_total_tax_inc = $totals_inc['quotation_total'];
        $base_total_tax_exc = $totals_exc['quotation_total'];*/
        $total_products_before_discount_wt = $totals_inc['quotation_total_before_discount'];
        $total_products_before_discount = $totals_exc['quotation_total_before_discount'];
        $total_products_wt = $totals_inc['quotation_total_products'];
        $total_products = $totals_exc['quotation_total_products'];

        $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            foreach ($discounts as &$discount) {
                $discount['discount_id'] = $discount['id_roja45_quotation_charge'];
                $discount['discount_name'] = $discount['charge_name'];
                $discount['discount_type'] = $discount['charge_type'];
                $discount['discount_method'] = $discount['charge_method'];
                if ($discount['charge_method'] == QuotationCharge::$PERCENTAGE) {
                    $discount['discount_value'] = $discount['charge_value'] . '%';
                    $discount['charge_amount'] = $total_products_before_discount * ((int) $discount['charge_value'] / 100);
                    $discount['charge_amount_wt'] = $total_products_before_discount_wt * ((int) $discount['charge_value'] / 100);
                } else {
                    $discount['charge_amount'] = (float) $discount['charge_value'];
                    $discount['charge_amount_wt'] = (float) $discount['charge_amount_wt'];
                    $discount['discount_value'] = RojaFortyFiveQuotationsProCore::formatPrice(
                        $discount['charge_value'],
                        $currency
                    );
                }
                $discount['charge_amount_tax'] = $discount['charge_amount_wt'] - $discount['charge_amount'] ;

                $discount['discount_amount'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $show_taxes ? $discount['charge_amount_wt'] : $discount['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $discount['discount_amount_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $show_taxes ? $discount['charge_amount_wt'] : $discount['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $discount['discount_amount_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $discount['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $discount['discount_amount_tax'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $discount['charge_amount_tax'],
                        $currency,
                        true
                    ),
                    $currency
                );
            }
        } else {
            foreach ($discounts as &$discount) {
                $discount['charge_value_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice($discount['charge_value']),
                    $currency
                );
                if ($discount['charge_method'] == QuotationCharge::$PERCENTAGE) {
                    $discount['amount'] = $total_products * ((int) $discount['charge_value'] / 100);
                    $discount['amount_wt'] = $total_products_wt * ((int) $discount['charge_value'] / 100);
                } elseif ($discount['charge_method'] == QuotationCharge::$VALUE) {
                    $discount['amount'] = (float) $discount['charge_value'];
                    $discount['amount_wt'] = (float) $discount['charge_value'];
                }

                $discount['amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    $discount['amount'],
                    $currency
                );
                $discount['amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    $discount['amount_wt'],
                    $currency
                );

                $charge_amount = $show_taxes ? $discount['amount_wt'] : $discount['amount'];
                $discount['charge_amount'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    $charge_amount,
                    $currency
                );
            }
        }

        $charges = $this->getQuotationAllCharges();
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            foreach ($charges as &$charge) {
                $charge['charge_id'] = $charge['id_roja45_quotation_charge'];
                $charge['charge_amount'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $show_taxes ? $charge['charge_amount_wt'] : $charge['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
            }
        } else {
            foreach ($charges as &$charge) {
                $charge['charge_amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $charge['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $charge['charge_amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $charge['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $charge_amount = $show_taxes ? $charge['charge_amount_wt'] : $charge['charge_amount'];
                $charge['charge_amount'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice($charge_amount),
                    $currency
                );
            }
        }

        $include_additional_shipping = true;
        if ($shipping = $this->getQuotationShippingCharges($this->id_lang)) {
            foreach($shipping as $shipper) {
                if ($shipper['charge_default'] && $shipper['free_shipping']) {
                    $include_additional_shipping = false;
                }
            }
        } else {
            $include_additional_shipping = false;
        }

        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            foreach ($shipping as &$charge) {
                $charge['charge_id'] = $charge['id_roja45_quotation_charge'];

                $charge['charge_amount_inc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $charge['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $charge['charge_amount_exc'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $charge['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $charge['charge_amount_tax'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $charge['charge_amount_wt'] - $charge['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $charge['charge_amount'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $show_taxes ? $charge['charge_amount_wt'] : $charge['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
            }
        } else {
            foreach ($shipping as &$charge) {
                $charge['charge_amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $charge['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $charge['charge_amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $charge['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $charge_amount = $show_taxes ? $charge['charge_amount_wt'] : $charge['charge_amount'];
                $charge['charge_amount'] = RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice($charge_amount),
                    $currency
                );
            }
        }

        $total_shipping_exc = $totals_exc['quotation_total_shipping'];
        $total_shipping_inc = $totals_inc['quotation_total_shipping'];
        $total_shipping_additional_exc = $totals_exc['quotation_total_additional_shipping'];
        $total_shipping_additional_inc = $totals_inc['quotation_total_additional_shipping'];

        if ($include_additional_shipping) {
            $total_shipping_exc += $total_shipping_additional_exc;
            $total_shipping_inc += $total_shipping_additional_inc;
            $totals_exc['quotation_total'] += $total_shipping_additional_exc;
            $totals_inc['quotation_total'] += $total_shipping_additional_inc;
        }
        // TODO - if not free shipping add additional to shipping total.
        $quotation_total_exc = $totals_exc['quotation_total'];
        $quotation_total_inc = $totals_inc['quotation_total'];

        $total_tax_products = $totals_inc['quotation_total_products'] - $totals_exc['quotation_total_products'];
        $total_tax_exc_discount = $totals_inc['quotation_total_before_discount'] - $totals_exc['quotation_total_before_discount'];
        $total_tax_inc_discount = $totals_inc['quotation_total'] - $totals_exc['quotation_total'];
        $total_tax_inc_shipping = $quotation_total_inc - $quotation_total_exc;

        $total_tax = $total_tax_inc_shipping;
        if ($total_tax < 0) {
            $total_tax = 0;
        }

        $total_handling_exc = $totals_exc['quotation_total_handling'];
        $total_handling_inc = $totals_inc['quotation_total_handling'];
        $total_charges_exc = $totals_exc['quotation_total_charges'];
        $total_charges_inc = $totals_inc['quotation_total_charges'];
        $total_discounts_exc = $totals_exc['quotation_total_discount'];
        $total_discounts_inc = $totals_inc['quotation_total_discount'];

        $total_customizations_exc = $totals_exc['quotation_total_customizations'];
        $total_customizations_inc = $totals_inc['quotation_total_customizations'];

        $total_ecotax_exc = $totals_exc['quotation_total_ecotax'];
        $total_ecotax_inc = $totals_inc['quotation_total_ecotax'];

        $exchange_rate = (Configuration::get('PS_CURRENCY_DEFAULT') == $this->id_currency) ? 1.0 : $currency->conversion_rate;

        $totals_data = array(
            'discounts' => $discounts,
            'charges' => $charges,
            'shipping' => $shipping,
            'total_charges_exc' => $total_charges_exc,
            'total_charges_inc' => $total_charges_inc,
            'quotation_charges' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_charges_inc : $total_charges_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'total_customizations_exc' => $total_customizations_exc,
            'total_customizations_inc' => $total_customizations_inc,
            'quotation_customizations_exc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_customizations_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_customizations_inc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_customizations_inc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_customizations' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_customizations_inc : $total_customizations_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'total_ecotax_exc' => $total_ecotax_exc,
            'total_ecotax_inc' => $total_ecotax_inc,
            'quotation_ecotax_exc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_ecotax_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_ecotax_inc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_ecotax_inc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_ecotax' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_ecotax_inc : $total_ecotax_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'total_discounts_exc' => $total_discounts_exc,
            'total_discounts_inc' => $total_discounts_inc,
            'quotation_discounts' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_discounts_inc : $total_discounts_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'total_shipping_exc' => $total_shipping_exc,
            'total_shipping_inc' => $total_shipping_inc,
            'quotation_shipping' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_shipping_inc : $total_shipping_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'total_handling_exc' => $total_handling_exc,
            'total_handling_inc' => $total_handling_inc,
            'quotation_handling' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_handling_inc : $total_handling_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'total_products_exc' => $total_products,
            'total_products_inc' => $total_products_wt,
            'quotation_subtotal' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_products_wt : $total_products,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_subtotal_exc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_products,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_subtotal_inc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_products_wt,
                    $currency,
                    true
                ),
                $currency
            ),
            'total_price_before_discount' => $totals_exc['quotation_total_before_discount'],
            'total_price_before_discount_exc' => $totals_exc['quotation_total_before_discount'],
            'total_price_before_discount_wt' => $totals_inc['quotation_total_before_discount'],
            'total_price_before_discount_inc' => $totals_inc['quotation_total_before_discount'],
            'quotation_products_before_discount' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $totals_inc['quotation_total_before_discount'] : $totals_exc['quotation_total_before_discount'],
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_products_before_discount_exc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_exc['quotation_total_before_discount'],
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_products_before_discount_inc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_inc['quotation_total_before_discount'],
                    $currency,
                    true
                ),
                $currency
            ),

            'total_products_after_discount' => $totals_exc['quotation_total_after_discount'],
            'total_products_after_discount_exc' => $totals_exc['quotation_total_after_discount'],
            'total_products_after_discount_wt' => $totals_inc['quotation_total_after_discount'],
            'total_products_after_discount_inc' => $totals_inc['quotation_total_after_discount'],
            'total_price' => $quotation_total_inc,
            'total_price_without_tax' => $quotation_total_exc,
            //'total_price_without_tax' => $base_total_tax_exc,
            'total_deposit' => 0,
            'total_deposit_wt' => 0,
            'total_deposit_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(0),
                $currency
            ),
            'total_deposit_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(0),
                $currency
            ),
            'total_tax_products' => $total_tax_products,
            'total_tax' => $total_tax,
            'tax_average_rate' => $this->getTaxesAverage(),
            'quotation_tax' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    ($quotation_total_inc - $quotation_total_exc),
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_total' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $quotation_total_inc : $quotation_total_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_total_exc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $quotation_total_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_total_inc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $quotation_total_inc,
                    $currency,
                    true
                ),
                $currency
            ),
            'default_currency_symbol' => Tools::strtoupper($defaultCurrency->iso_code),
            'quotation_currency_symbol' => Tools::strtoupper($currency->iso_code),
            'deleted' => ($status->id == (int) Configuration::get('ROJA45_QUOTATIONSPRO_STATUS_DLTD')),
            'show_exchange_rate' => ((float) $exchange_rate == (float) 1.0) ? 0 : 1,
            'exchange_rate' => $exchange_rate,
        );

        $quotation_data = array_merge(
            $quotation_data,
            $totals_data
        );

        if (!(int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
            $legacy_data = array(
                'requested_products' => array_values($products),
                'total_charges' => $total_charges_exc,
                'total_discounts' => $total_discounts_exc,
                'total_charges_formatted' => Tools::displayPrice(
                    Tools::convertPrice(
                        $totals_data['total_charges_exc'],
                        $currency,
                        true
                    ),
                    $currency
                ),
                'total_charges_wt_formatted' => Tools::displayPrice(
                    Tools::convertPrice(
                        $totals_data['total_charges_inc'],
                        $currency,
                        true
                    ),
                    $currency
                ),
                'total_discounts_formatted' => Tools::displayPrice(
                    Tools::convertPrice(
                        $totals_data['total_discounts_exc'],
                        $currency,
                        true
                    ),
                    $currency
                ),
                'total_discounts_wt_formatted' => Tools::displayPrice(
                    Tools::convertPrice(
                        $totals_data['total_discounts_inc'],
                        $currency,
                        true
                    ),
                    $currency
                ),
                'total_products_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $totals_data['total_products_exc'],
                        $currency,
                        true
                    ),
                    $currency
                ),
                'total_products_wt_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                    Tools::convertPrice(
                        $totals_data['total_products_inc'],
                        $currency,
                        true
                    ),
                    $currency
                ),
            );

            $quotation_data = array_merge(
                $quotation_data,
                $legacy_data
            );
        }

        $totals_formatted = array(
            'total_ecotax_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_ecotax_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_ecotax_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_ecotax_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_customizations_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_customizations_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_customizations_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_customizations_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_charges_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_charges_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_charges_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_charges_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_discounts_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_discounts_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_discounts_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_discounts_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_shipping_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_shipping_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_shipping_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_shipping_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_handling_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_handling_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_handling_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_handling_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_products_exc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_products_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_products_inc_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_products_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_price_before_discount_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_price_before_discount'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_price_before_discount_wt_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_price_before_discount_wt'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_products_after_discount_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_products_after_discount'],
                    //$totals_data['total_products_after_discount'] + $totals_data['total_ecotax_exc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_products_after_discount_wt_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_products_after_discount_wt'],
                    //$totals_data['total_products_after_discount_wt'] + $totals_data['total_ecotax_inc'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_price_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_price'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_tax_products_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_tax_products'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_tax_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_tax'],
                    $currency,
                    true
                ),
                $currency
            ),
            'total_price_without_tax_formatted' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $totals_data['total_price_without_tax'],
                    $currency,
                    true
                ),
                $currency
            ),
        );

        $quotation_data = array_merge(
            $quotation_data,
            $totals_formatted
        );

        if ($additional_quotation_datas = Hook::exec(
            'actionGetAdditionalQuotationSummaryData',
            [
                'summary' => $quotation_data,
            ],
            null,
            true
        )) {
            foreach ($additional_quotation_datas as $key => $token) {
                $tokens = [];
                foreach ($token as $token_key => $value) {
                    $tokens[$key . '_' . $token_key] = $value;
                }
                $quotation_data = array_merge(
                    $quotation_data,
                    $tokens
                );
            }
        }

        return $quotation_data;
    }

    public function isStatus($status_code)
    {
        $id_roja45_quotation_status = (int) QuotationStatus::getQuotationStatusByType($status_code);
        return ($this->id_roja45_quotation_status == $id_roja45_quotation_status);
    }

    public function setStatus($status_name, $template_vars = array(), $attachments = null, $no_emails = false, $email_content = null, $subject = null)
    {
        $context = Context::getContext();
        $id_quotation_status = Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_STATUS_' . $status_name);
        $status = new QuotationStatus(
            $id_quotation_status,
            $context->language->id
        );

        if (!Validate::isLoadedObject($status)) {
            throw new Exception('Unable to load status [' . $status_name . ']');
        }

        if ($no_emails || empty($this->email)) {
            $this->id_roja45_quotation_status = $status->id;
            $this->save();
            return true;
        }

        $contact = false;
        if (Tools::getValue('ROJA45_QUOTATIONSPRO_USE_CS') == 1) {
            $contact = new Contact(Configuration::get('ROJA45_QUOTATIONSPRO_CS_ACCOUNT'), $context->language->id);
            $contact_name = $contact->name;
            $contact_email = $contact->email;
        } else {
            $contacts = Contact::getContacts($context->language->id);
            if (!count($contacts)) {
                throw new Exception('No customer service account available for message thread');
            }
            $contact = new Contact($contacts[0]['id_contact']);
            if (!Validate::isLoadedObject($contact)) {
                throw new Exception('Unable to find the default customer service account.');
            }
            $contact_name = Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_NAME');
            $contact_email = Configuration::get('ROJA45_QUOTATIONSPRO_EMAIL');
        }

        $replyTo = $contact_email;
        $replyToName = $contact_name;
        if (Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER')) {
            $employee = new Employee($this->id_employee);
            $replyTo = $employee->email;
            $replyToName = $employee->firstname . ' ' . $employee->lastname;
        }
        $to_list = array($this->email);
        $to_names = array($this->firstname . ' ' . $this->lastname);

        if (!$bcc = Configuration::get('ROJA45_QUOTATIONSPRO_CONTACT_BCC')) {
            $bcc = null;
        }

        // TODO - this should come from status setting
        $sent = 1;
        if ($status->send_email) {
            if ($id_thread = QuotationMessage::getCustomerThread(
                $this->id
            )) {
                $ct = new CustomerThread($id_thread);
                if (Validate::isLoadedObject($ct)) {
                    $thread_id = $ct->id;
                }
            } else {
                $ct = new CustomerThread();
                $ct->email = $this->email;
                $ct->id_contact = $contact->id;
                $ct->id_lang = (int) $this->id_lang;
                $ct->id_shop = $this->id_shop;
                $ct->id_customer = $this->id_customer;
                $ct->status = 'open';
                $ct->token = $this->reference;
                $ct->add();
                $thread_id = $ct->id;

                $quotation_message = new QuotationMessage();
                $quotation_message->id_roja45_quotation = $this->id;
                $quotation_message->id_customer_thread = $ct->id;
                $quotation_message->add();
            }

            if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_USE_PS_PDF')) {
                $quotation_answer = new QuotationAnswer(
                    $status->id_roja45_quotation_answer,
                    $context->language->id
                );
                if (Validate::isLoadedObject($quotation_answer)) {
                    // get customer pdfs for status.
                    //$attachments = null;

                    if (!$attachments) {
                        if ($status->customer_pdf_ids && ($customer_pdf_ids = explode(',', $status->customer_pdf_ids))) {
                            $attachments = array();
                            foreach ($customer_pdf_ids as $customer_pdf_id) {
                                if ($customer_pdf_id) {
                                    $pdf = new QuotationAnswer($customer_pdf_id, $this->id_lang);
                                    $name_clean = str_replace(' ', '_', $pdf->name);
                                    $name_clean = Tools::strtolower($name_clean) . '_' . $customer_pdf_id;

                                    $attachments[$name_clean]['content'] = RojaPDF::generatePDF(
                                        'CustomPdf',
                                        $this,
                                        false,
                                        array(
                                            'id_roja45_quotation_answer' => $customer_pdf_id,
                                        )
                                    );
                                    $attachments[$name_clean]['name'] = $pdf->name . '.pdf';
                                    $attachments[$name_clean]['mime'] = 'application/pdf';
                                }
                            }
                        }
                    }

                    if (!$email_content) {
                        $email_content = $quotation_answer->compileTemplate($template_vars);
                    }

                    if (!$subject) {
                        $subject = $quotation_answer->subject . ' [#tc' . $this->reference . '|#ct' . $thread_id . ']';

                        if ((int) Configuration::get(
                            'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'
                        )) {
                            $subject = $quotation_answer->subject;
                            RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                                'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_TC',
                                $this->reference
                            );
                            RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                                'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_CT',
                                $thread_id
                            );
                        }
                    }

                    if (!$sent = Mail::Send(
                        (int) $this->id_lang,
                        'message_wrapper',
                        $subject,
                        $email_content,
                        $to_list,
                        $to_names,
                        $contact_email,
                        $contact_name,
                        $attachments,
                        null,
                        _PS_MODULE_DIR_ . 'roja45quotationspro/mails/',
                        false,
                        null,
                        $bcc,
                        $replyTo,
                        $replyToName
                    )) {
                        PrestaShopLogger::addLog(
                            'Roja45: Unable to send customer email',
                            3,
                            null,
                            'AdminQuotationsPro'
                        );
                        $sent = 1;
                        //throw new Exception('Unable to send customer email');
                    }

                    $employee_id = $this->id_employee;
                    if (!$this->id_employee) {
                        $admin_user = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_)[0];
                        $employee_id = $admin_user['id_employee'];
                    }

                    $message_content = $email_content['{content_txt}'];
                    if (empty($message_content)) {
                        $customer_form_txt = \Soundasleep\Html2Text::convert(
                            $email_content['{content_html}'],
                            [
                                'ignore_errors' => true
                            ]
                        );
                    }

                    if (empty($message_content)) {
                        $message_content = 'n/a';
                    }
                    $customer_message = new CustomerMessage();
                    $customer_message->id_customer_thread = $ct->id;
                    $customer_message->id_employee = $employee_id;
                    $customer_message->message = $message_content;
                    $customer_message->add();
                }

                if ($status->notify_admin) {
                    $quotation_answer = new QuotationAnswer(
                        $status->id_roja45_quotation_answer_admin,
                        $this->id_lang
                    );
                    if (!Validate::isLoadedObject($quotation_answer)) {
                        throw new Exception('Unable to load admin email ['.$quotation_answer->template.']');
                    }
                    $attachments = null;
                    if ($status->admin_pdf_ids && ($admin_pdf_ids = explode(',', $status->admin_pdf_ids))) {
                        $attachments = array();
                        foreach ($admin_pdf_ids as $admin_pdf_id) {
                            if ($admin_pdf_id) {
                                $pdf = new QuotationAnswer($admin_pdf_id, $this->id_lang);
                                $name_clean = str_replace(' ', '_', $pdf->name);
                                $name_clean = strtolower($name_clean) . '_' . $admin_pdf_id;

                                $attachments[$name_clean]['content'] = RojaPDF::generatePDF(
                                    'CustomPdf',
                                    $this,
                                    false,
                                    array(
                                        'id_roja45_quotation_answer' => $admin_pdf_id,
                                    )
                                );
                                $attachments[$name_clean]['name'] = $pdf->name . '.pdf';
                                $attachments[$name_clean]['mime'] = 'application/pdf';
                            }
                        }
                    }

                    if ($status->id_roja45_quotation_answer != $status->id_roja45_quotation_answer_admin) {
                        $email_content = $quotation_answer->compileTemplate($template_vars);
                    }

                    if (!$sent = Mail::Send(
                        (int) $this->id_lang,
                        'message_wrapper',
                        $quotation_answer->subject,
                        $email_content,
                        $contact_email,
                        $contact_name,
                        $contact_email,
                        $contact_name,
                        $attachments,
                        null,
                        _PS_MODULE_DIR_ . 'roja45quotationspro/mails/',
                        false,
                        null,
                        $bcc,
                        $this->email,
                        $this->firstname . ' ' . $this->lastname
                    )) {
                        PrestaShopLogger::addLog(
                            'Roja45: Unable to send admin email',
                            3,
                            null,
                            'AdminQuotationsPro'
                        );
                        $sent = 1;
                        //throw new Exception('Unable to send admin email');
                    }
                }
            } else {
                $subject = sprintf(
                    Module::getInstanceByName(
                        'roja45quotationspro'
                    )->l('Your quotation [%1$s] : [#ct%2$s] : [#tc%3$s]'),
                    $this->reference,
                    $thread_id,
                    $this->reference
                );

                if (!$template = $status->answer_template) {
                    $template = 'message_wrapper';
                    if ($status->code == QuotationStatus::$RCVD) {
                        $template = 'roja45quotationrequest';
                    } elseif ($status->code == QuotationStatus::$SENT) {
                        $template = 'message_wrapper';
                    }
                }

                $template_vars = array_merge($template_vars, $this->getTemplateVars());
                if ((int) Configuration::get(
                    'ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'
                )) {
                    $subject = sprintf(
                        Module::getInstanceByName(
                            'roja45quotationspro'
                        )->l('Your quotation [%1$s]'),
                        $this->reference
                    );
                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                        'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_TC',
                        $this->reference
                    );
                    RojaFortyFiveQuotationsProCore::saveCustomerRequirement(
                        'ROJA45QUOTATIONSPRO_CUSTOMER_THREAD_CT',
                        $thread_id
                    );
                }

                $sent = Mail::Send(
                    (int) $this->id_lang,
                    $template,
                    $subject,
                    $template_vars,
                    $to_list,
                    $to_names,
                    $contact_email,
                    //$contact_name,
                    null,
                    $attachments,
                    null,
                    _PS_ROOT_DIR_ . '/modules/roja45quotationspro/mails/',
                    false,
                    (int) $this->id_shop
                );

                if ($sent && $status->notify_admin) {
                    $template_vars = array(
                        'quotation_reference' => $this->reference,
                        'quotation_status' => $status->code,
                    );
                    $sent = Mail::Send(
                        (int) $this->id_lang,
                        'quotationspro_notify_admin',
                        sprintf(
                            Mail::l('Status update for quotation %s', (int) Context::getContext()->language->id),
                            $this->reference
                        ),
                        $template_vars,
                        $contact_email,
                        $contact_name,
                        $contact_email,
                        //$contact_name,
                        null,
                        null,
                        null,
                        _PS_ROOT_DIR_ . '/modules/roja45quotationspro/mails/',
                        false,
                        (int) $this->id_shop
                    );
                    if (!$sent) {
                        return false;
                    }
                }
            }
        }

        $this->id_roja45_quotation_status = $status->id;
        $this->save();
        return $sent;
    }

    /**
     * getPriceWithoutTax - Calculate the price without tax added.
     *
     * @param Product $id_product Product ID of the price to be converted
     * @param double $price Price to be converted
     * @param Context $context The current user context
     *
     * @return json
     *
     */
    public function getPriceWithoutTax($id_product, $price, $context, $address = null)
    {
        if (!$address) {
            $address = $this->getTaxAddress();
        }

        $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int) $id_product, $context);
        $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
        $rate = (double) $tax_calculator->getTotalRate();

        return (double) $price / (1 + ($rate / 100));
    }

    public function getPriceWithTax($id_product, $price, $context, $address = null)
    {
        if (!$address) {
            $address = $this->getTaxAddress();
        }

        $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int) $id_product, $context);
        $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
        $rate = (double) $tax_calculator->getTotalRate();

        return $price * (1 + ($rate / 100));
    }

    public function getRateAndPriceWithTax($id_product, $price, $context, $address = null)
    {
        if (!$address) {
            $address = $this->getTaxAddress();
        }

        $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int) $id_product, $context);
        $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
        $rate = (double) $tax_calculator->getTotalRate();

        return array(
            'rate' => $rate,
            'price' => $price * (1 + ($rate / 100)),
        );
    }

    public function getSmartyVars()
    {
        $context = Context::getContext();
        $currency = new Currency($this->id_currency);
        $customer = new Customer((int) $this->id_customer);
        $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
        foreach ($discounts as &$discount) {
            $discount['charge_value_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($discount['charge_value']),
                $currency
            );
        }
        $charges = $this->getQuotationAllCharges();
        foreach ($charges as &$charge) {
            $charge['charge_amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($charge['charge_amount']),
                $currency
            );
            $charge['charge_amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($charge['charge_amount_wt']),
                $currency
            );
        }

        $shop_address_obj = $context->shop->getAddress();
        if (isset($shop_address_obj) && $shop_address_obj instanceof Address) {
            $shop_address = AddressFormat::generateAddress($shop_address_obj, array(), '<br />');
        }

        $date_received = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $this->date_add
        );
        $date_received_formatted = $date_received->format($context->language->date_format_lite);
        $logo = '';
        if (Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = $context->link->getMediaLink(_PS_IMG_ . Configuration::get(
                'PS_LOGO_INVOICE',
                null,
                null,
                (int) Shop::getContextShopID()
            ));
        } elseif (Configuration::get('PS_LOGO_MAIL', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = $context->link->getMediaLink(_PS_IMG_ . Configuration::get(
                'PS_LOGO_MAIL',
                null,
                null,
                (int) Shop::getContextShopID()
            ));
        }  elseif (Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = $context->link->getMediaLink(_PS_IMG_ . Configuration::get(
                'PS_LOGO',
                null,
                null,
                (int) Shop::getContextShopID()
            ));
        }

        $date_now = new DateTime('now');
        $date_now_formatted = $date_now->format($context->language->date_format_lite);

        $status = new QuotationStatus($this->id_roja45_quotation_status, (int) $context->language->id);
        $language = new Language($this->id_lang);
        $languages = Language::getLanguages();
        $template_vars = array(
            'languages' => $languages,
            'language' => $language,
            'shop_address' => $shop_address,
            'defaultFormLanguage' => (int) $context->language->id,
            'quotation' => $this,
            'discounts' => $discounts,
            'charges' => $charges,
            'email' => true,
            'show_taxes' => $this->calculate_taxes,
            'shop_logo' => $logo,
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'shop_email' => Configuration::get('PS_SHOP_EMAIL'),
            'shop_fax' => Configuration::get(
                'PS_SHOP_FAX',
                null,
                null,
                $context->language->id
            ),
            'shop_phone' => Configuration::get(
                'PS_SHOP_PHONE',
                null,
                null,
                $context->language->id
            ),
            'shop_url' => $context->link->getPageLink(
                'index',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            'my_account_url' => $context->link->getPageLink(
                'my-account',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            'guest_tracking_url' => $context->link->getPageLink(
                'guest-tracking',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            'history_url' => $context->link->getPageLink(
                'history',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            'my_quotes_link' => $context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'action' => 'getCustomerQuotes',
                ),
                true
            ),
            'purchase_link' => $context->link->getModuleLink(
                'roja45quotationspro',
                'QuotationsProFront',
                array(
                    'p' => $this->id,
                ),
                true
            ),
            'email' => $this->email,
            'customer_email' => $this->email,
            'firstname' => $this->firstname,
            'customer_firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'customer_lastname' => $this->lastname,
            'quotation_reference' => $this->reference,
            'quotation_received' => $this->date_add,
            'quotation_received_formatted' => $date_received_formatted,
            'date_now_formatted' => $date_now_formatted,
            'quotation_status_code' => $status->code,
            'quotation_status' => $status->status,
            'quotation_lastupdate' => $this->date_upd,
            'quotation_total' => $this->total_to_pay,
            'quotation_total_wt' => $this->total_to_pay_wt,
            'use_taxes' => (int) $this->calculate_taxes,
        );

        $summary = $this->getSummaryDetails($context->language->id, $this->id_currency, $this->calculate_taxes);
        $template_vars = array_merge($template_vars, $summary);

        // create a product list template in html and txt

        if (Validate::isLoadedObject($customer)) {
            $customer_vars = array(
                'customer_email' => $customer->email,
                'customer_firstname' => $customer->firstname,
                'customer_lastname' => $customer->lastname,
                'customer_company' => $customer->company,
            );
            $template_vars = array_merge($template_vars, $customer_vars);
        }

        $requestJSON = json_decode($this->form_data);
        if (isset($requestJSON->columns)) {
            foreach ($requestJSON->columns as $column) {
                foreach ($column->fields as $field) {
                    if (($field->name != 'FIRSTNAME') &&
                        ($field->name != 'LASTNAME') &&
                        ($field->name != 'CONTACT_EMAIL')) {
                        $template_vars['' . $field->name . '_LABEL'] = $field->label;
                        $template_vars['' . $field->name . '_VALUE'] = $field->value;
                    }
                }
            }
        }
        return $template_vars;
    }

    public function getTemplateVars()
    {
        $context = Context::getContext();
        $customer = new Customer((int) $this->id_customer);
        $currency = new Currency($this->id_currency);
        $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
        foreach ($discounts as &$discount) {
            $discount['charge_value_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($discount['charge_value']),
                $currency
            );
        }
        $charges = $this->getQuotationAllCharges();
        foreach ($charges as &$charge) {
            $charge['charge_amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($charge['charge_amount']),
                $currency
            );
            $charge['charge_amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($charge['charge_amount_wt']),
                $currency
            );
        }

        $logo = '';
        if (Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_INVOICE', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = $context->link->getMediaLink(_PS_IMG_ . Configuration::get(
                'PS_LOGO_INVOICE',
                null,
                null,
                (int) Shop::getContextShopID()
            ));
        } elseif (Configuration::get('PS_LOGO_MAIL', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = $context->link->getMediaLink(_PS_IMG_ . Configuration::get(
                'PS_LOGO_MAIL',
                null,
                null,
                (int) Shop::getContextShopID()
            ));
        }  elseif (Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()) != false &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, (int) Shop::getContextShopID()))
        ) {
            $logo = $context->link->getMediaLink(_PS_IMG_ . Configuration::get(
                'PS_LOGO',
                null,
                null,
                (int) Shop::getContextShopID()
            ));
        }

        $status = new QuotationStatus($this->id_roja45_quotation_status, (int) $context->language->id);
        $language = new Language($this->id_lang);
        $languages = Language::getLanguages();
        $tmp_vars = array(
            'languages' => $languages,
            'language' => $language,
            'defaultFormLanguage' => (int) $context->language->id,
            'quotation' => $this,
            'discounts' => $discounts,
            'charges' => $charges,
            'email' => true,
            'show_taxes' => $this->calculate_taxes,
        );

        $summary = $this->getSummaryDetails($context->language->id, $this->id_currency, $this->calculate_taxes);
        $vars = array_merge($tmp_vars, $summary);

        $tpl = $context->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'roja45quotationspro/views/templates/admin/' . 'quote_template_txt.tpl'
        );
        $tpl->assign($vars);
        $tpl->assign(
            array(
                'languages' => $languages,
                'link' => $context->link,
            )
        );
        $quote_txt = $tpl->fetch();
        $tpl = $context->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'roja45quotationspro/views/templates/admin/' . 'quote_template.tpl'
        );
        $tpl->assign($vars);
        $tpl->assign(
            array(
                'languages' => $languages,
                'link' => $context->link,
            )
        );
        $quote_html = $tpl->fetch();

        // create a product list template in html and txt
        $template_vars = array(
            '{shop_logo}' => $logo,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_email}' => Configuration::get('PS_SHOP_EMAIL'),
            '{shop_url}' => $context->link->getPageLink(
                'index',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            '{my_account_url}' => $context->link->getPageLink(
                'my-account',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            '{guest_tracking_url}' => $context->link->getPageLink(
                'guest-tracking',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            '{history_url}' => $context->link->getPageLink(
                'history',
                true,
                $context->language->id,
                null,
                false,
                $context->shop->id
            ),
            '{email}' => $this->email,
            '{customer_email}' => $this->email,
            '{firstname}' => $this->firstname,
            '{customer_firstname}' => $this->firstname,
            '{lastname}' => $this->lastname,
            '{customer_lastname}' => $this->lastname,
            '{quotation_reference}' => $this->reference,
            '{quotation_received}' => $this->date_add,
            '{quotation_status_code}' => $status->code,
            '{quotation_status}' => $status->status,
            '{quotation_lastupdate}' => $this->date_upd,
            '{quotation_total}' => $this->total_to_pay,
            '{quotation_total_wt}' => $this->total_to_pay_wt,
            '{quote_html}' => $quote_html,
            '{quotation_summary_html}' => $quote_html,
            '{quote_txt}' => $quote_txt,
            '{quotation_summary_txt}' => $quote_txt,
        );

        foreach ($summary as $key => $row) {
            if (!is_array($row)) {
                $summary['{' . $key . '}'] = $summary[$key];
            }
            unset($summary[$key]);
        }

        $template_vars = array_merge($template_vars, $summary);

        if (Validate::isLoadedObject($customer)) {
            $customer_vars = array(
                '{customer_email}' => $customer->email,
                '{customer_firstname}' => $customer->firstname,
                '{customer_lastname}' => $customer->lastname,
                '{customer_company}' => $customer->company,
            );
            $template_vars = array_merge($template_vars, $customer_vars);
        }

        $requestJSON = json_decode($this->form_data);
        if (isset($requestJSON->columns)) {
            foreach ($requestJSON->columns as $column) {
                foreach ($column->fields as $field) {
                    if (($field->name != 'FIRSTNAME') &&
                        ($field->name != 'LASTNAME') &&
                        ($field->name != 'CONTACT_EMAIL')) {
                        $template_vars['{' . $field->name . '_LABEL}'] = $field->label;
                        $template_vars['{' . $field->name . '_VALUE}'] = $field->value;
                    }
                }
            }
        }
        return $template_vars;
    }

    public function getWsQuotationProducts()
    {
        $sql = new DbQuery();
        $sql->select(
            'qp.id_roja45_quotation_product as id,
            qp.id_product,
            qp.id_product_attribute,
            qp.qty,
            pl.name,
            qp.date_add,
            qp.date_upd'
        );
        $sql->from('roja45_quotationspro_product', 'qp');
        $sql->leftJoin(
            'product',
            'p',
            'qp.id_product = p.id_product'
        );
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.id_product = pl.id_product AND pl.id_lang=' . (int) Context::getContext()->language->id
        );
        $sql->where('id_roja45_quotation = ' . (int) $this->id);
        return Db::getInstance()->executeS($sql);
    }

    public function getWsQuotationOrders()
    {
        $sql = new DbQuery();
        $sql->select(
            'qo.id_roja45_quotation_order as id,
            qo.id_order,
            qo.date_add,
            qo.date_upd'
        );
        $sql->from('roja45_quotationspro_order', 'qo');
        $sql->where('id_roja45_quotation = ' . (int) $this->id);
        return Db::getInstance()->executeS($sql);
    }

    public function hasExpired()
    {
        if ($this->expiry_date != '0000-00-00 00:00:00') {
            $date = new DateTime($this->expiry_date);
            if (new DateTime() > $date) {
                return true;
            }
        }
        return false;
    }

    public function delete()
    {
        // get all products, delete them
        $quotation_products = $this->getQuotationProductList();
        foreach ($quotation_products as $quotation_product) {
            $quotation_product = new QuotationProduct($quotation_product['id_roja45_quotation_product']);
            $quotation_product->delete();
        }
        $quotation_orders = $this->getQuotationOrderList();
        foreach ($quotation_orders as $quotation_order) {
            $quotation_order = new QuotationOrder($quotation_order['id_roja45_quotation_order']);
            $quotation_order->delete();
        }
        QuotationMessage::deleteQuotationMessages($this->id_roja45_quotation);
        // get all discounts, delete them
        // TODO - Delete customizations

        $quotation_request = new QuotationRequest($this->id_request);
        if (Validate::isLoadedObject($quotation_request)) {
            $quotation_request->delete();
        }
        return parent::delete();
    }

    public function save($null_values = false, $autodate = true)
    {
        $return = parent::save($null_values, $autodate);
        $this->is_dirty = true;
        return $return;
    }

    public function getEmailTemplateContent($template_name, $mail_type, $vars, $context, $iso_code = null)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        if (!$iso_code) {
            $iso_code = $context->language->iso_code;
        }
        $theme_template_path = _PS_THEME_DIR_ .
            'modules/roja45quotationspro/mails' .
            DIRECTORY_SEPARATOR . $iso_code . DIRECTORY_SEPARATOR . $template_name;
        $default_mail_template_path = _PS_MODULE_DIR_ .
            'roja45quotationspro/mails' .
            DIRECTORY_SEPARATOR . $iso_code . DIRECTORY_SEPARATOR . $template_name;

        if (Tools::file_exists_cache($theme_template_path)) {
            $default_mail_template_path = $theme_template_path;
        }

        if (Tools::file_exists_cache($default_mail_template_path)) {
            $context->smarty->assign($vars);
            return $context->smarty->fetch($default_mail_template_path);
        }
        return '';
    }

    public function getTaxAddress()
    {
        if ((int) $this->id_address_invoice && $this->id_address_tax == RojaQuotation::TAX_INVOICE_ADDRESS) {
            return new Address($this->id_address_invoice);
        } else if ((int) $this->id_address_delivery && $this->id_address_tax == RojaQuotation::TAX_DELIVERY_ADDRESS) {
            return new Address($this->id_address_delivery);
        } else {
            $address = new Address();
            $address->id_country = isset($this->id_country) ? $this->id_country : Configuration::get('PS_COUNTRY_DEFAULT');

            if ((int) $this->id_state) {
                $address->id_state = (int) $this->id_state;
            }
            $address->postcode = 0;
            return $address;
        }
    }

    /**\
     * @param Cart $cart
     * @return void
     * @throws PrestaShopDatabaseExceptio
     *
     */
    public function resetCart($cart, $delete_products = true)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $sql = new DbQuery();
            $sql->select('cp.`id_product_attribute`, cp.`id_product`');
            $sql->from('cart_product', 'cp');
            $sql->where('cp.`id_cart` = ' . (int) $cart->id);
            if ($products = Db::getInstance()->executeS($sql)) {
                foreach ($products as $cart_product) {
                    if ($delete_products) {
                        $cart->deleteProduct(
                            $cart_product['id_product'],
                            $cart_product['id_product_attribute']
                        );
                    }

                    RojaFortyFiveQuotationsProCore::deleteCustomerCartProductSpecificPrice(
                        $this->id_customer,
                        $cart->id,
                        $cart_product['id_product'],
                        $cart_product['id_product_attribute']
                    );
                }
            }
        } else {
            $sql = new DbQuery();
            $sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`id_customization`');
            $sql->from('cart_product', 'cp');
            $sql->where('cp.`id_cart` = ' . (int) $cart->id);
            if ($products = Db::getInstance()->executeS($sql)) {
                foreach ($products as $cart_product) {
                    if ($delete_products) {
                        $cart->deleteProduct(
                            $cart_product['id_product'],
                            $cart_product['id_product_attribute'],
                            $cart_product['id_customization']
                        );
                    }
                    RojaFortyFiveQuotationsProCore::deleteCustomerCartProductSpecificPrice(
                        $this->id_customer,
                        $cart->id,
                        $cart_product['id_product'],
                        $cart_product['id_product_attribute']
                    );
                }
            }
        }
    }

    public function populateCart($products, $id_currency)
    {
        $context = Context::getContext();
        $precision = RojaFortyFiveQuotationsProCore::getComputingPrecision($context->currency);
        if (!$context->cart->id) {
            $context->cart->add();
            if ($context->cart->id) {
                $context->cookie->id_cart = (int) $context->cart->id;
            }
        }

        if (empty($context->cart->id_carrier)) {
            $context->cart->id_carrier = $this->id_carrier;
            $context->cart->update();
            CartRule::autoRemoveFromCart($context);
            CartRule::autoAddToCart($context);
        }

        $mysql_date_now = date('Y-m-d H:i:s', strtotime("-5 minutes"));
        $mysql_date_plus_one = date('Y-m-d H:i:s', strtotime('+1 day'));

        $disable_discount = (int)Configuration::get('ROJA45_QUOTATIONSPRO_DISABLECARTRULES');

        foreach ($context->cart->getProducts(true) as $cart_product) {
            $context->cart->deleteProduct(
                $cart_product['id_product'],
                $cart_product['id_product_attribute'],
                $cart_product['id_customization']
            );
        }

        $cart_rules = CartRule::getCustomerCartRules(
            (int) $context->language->id,
            $this->id_customer,
            true
        );
        foreach ($cart_rules as $cart_rule) {
            if ($disable_discount || $cart_rule['id_customer']) {
                $cart_rule = new CartRule($cart_rule['id_cart_rule']);
                $cart_rule->delete();
            }
        }

        $defaultCurrency = Currency::getDefaultCurrency();
        $currencies = Currency::getCurrencies(true, false, true);
        foreach ($products as $product) {
            $quotationProduct = new QuotationProduct($product['id_roja45_quotation_product']);
            if ($quotationProduct->id_product) {
                RojaFortyFiveQuotationsProCore::deleteCustomerCartProductSpecificPrice(
                    $this->id_customer,
                    $context->cart->id,
                    $quotationProduct->id_product,
                    $quotationProduct->id_product_attribute
                );
            };
            $specific_price_output = null;
            $orig_price_exc = Product::getPriceStatic(
                $product['id_product'],
                false,
                $product['id_product_attribute'],
                6,
                null,
                false,
                true,
                1,
                false,
                0,
                0,
                null,
                $specific_price_output,
                true, // $with_ecotax
                true, // $use_group_reduction
                $context,
                true,
                null
            );
            $orig_price_exc_no_ecotax = Product::getPriceStatic(
                $product['id_product'],
                false,
                $product['id_product_attribute'],
                6,
                null,
                false,
                true,
                1,
                false,
                0,
                0,
                null,
                $specific_price_output,
                false, // $with_ecotax
                true, // $use_group_reduction
                $context,
                true,
                null
            );
            $ecotax = $orig_price_exc - $orig_price_exc_no_ecotax;

            $customization_cost = 0;
            if ($quotationProduct->customization_cost_type == 1) {
                $customization_cost = $quotationProduct->customization_cost_exc / $quotationProduct->qty;
            } else {
                $customization_cost = $quotationProduct->customization_cost_exc;
            }

            $quote_price_tax_excl = Tools::convertPrice(
                $quotationProduct->unit_price_tax_excl + $customization_cost,
                $id_currency,
                true
            );

            foreach ($currencies as $currency) {
                if ($currency->id != $id_currency) {
                    if ($id_currency != $defaultCurrency->id) {
                        $price_exc = Tools::convertPrice(
                            $orig_price_exc,
                            $id_currency,
                            false
                        );
                        $unit_price_tax_excl = Tools::convertPrice(
                            $quote_price_tax_excl,
                            $id_currency,
                            false
                        );
                        $ecotax = Tools::convertPrice(
                            $ecotax,
                            $id_currency,
                            false
                        );
                    } else {
                        $price_exc = Tools::convertPrice(
                            $orig_price_exc,
                            $currency->id,
                            true
                        );
                        $unit_price_tax_excl = Tools::convertPrice(
                            $quote_price_tax_excl,
                            $currency->id,
                            true
                        );
                        $ecotax = Tools::convertPrice(
                            $ecotax,
                            $id_currency,
                            true
                        );
                    }
                } else {
                    $price_exc = $orig_price_exc;
                    $unit_price_tax_excl = $quote_price_tax_excl;
                }

                $reduction = Tools::ps_round($price_exc - $unit_price_tax_excl, 6);
                $id_group = Customer::getDefaultGroupId($this->id_customer);
                $group_reduction = GroupReduction::getValueForProduct($product['id_product'], $id_group);
                if ($group_reduction === false) {
                    $group_reduction = Group::getReductionByIdGroup($id_group);
                } else {
                    $group_reduction = $group_reduction * 100;
                }

                if ($group_reduction > 0) {
                    if ($price_exc > $unit_price_tax_excl) {
                        $price_exc = Tools::ps_round($price_exc / (1 - ($group_reduction / 100)), 6);
                        $reduction = Tools::ps_round(
                            $reduction / (1 - ($group_reduction / 100)),
                            6
                        );
                    } else {
                        $price_exc = Tools::ps_round($unit_price_tax_excl / (1 - ($group_reduction / 100)), 6);
                    }
                } else {
                    if ($price_exc < $unit_price_tax_excl) {
                        $price_exc = Tools::ps_round($unit_price_tax_excl, 6);
                    } else {
                        $price_exc = Tools::ps_round($price_exc, 6);
                    }
                }

                $specific_price = new SpecificPrice();
                $specific_price->id_cart = (int) $context->cart->id;
                $specific_price->id_shop = (int) $context->shop->id;
                $specific_price->id_shop_group = (int) $context->shop->id_shop_group;
                //$specific_price->id_currency = 0;
                $specific_price->id_currency = $currency->id;
                $specific_price->id_country = 0;
                $specific_price->id_group = 0;
                $specific_price->id_customer = (int) $this->id_customer;
                $specific_price->id_product = (int) $product['id_product'];
                $specific_price->id_product_attribute = (int) $product['id_product_attribute'];
                $specific_price->price = $price_exc - $ecotax;
                $specific_price->reduction_type = 'amount';
                $specific_price->reduction_tax = 0;
                $specific_price->from_quantity = 1;
                $specific_price->from = $mysql_date_now;
                $specific_price->to = $mysql_date_plus_one;

                if ($reduction > 0) {
                    $specific_price->reduction = Tools::ps_round($reduction, 6);
                    if (!$specific_price->save()) {
                        $module = Module::getInstanceByName('roja45quotationspro');
                        throw new Exception(
                            sprintf(
                                $module->l('Unable to create cart price for product %s : %s'),
                                $product['id_product'],
                                Db::getInstance()->getMsgError()
                            )
                        );
                    }
                    if ($currency->id == $id_currency) {
                        $quotationProduct->id_specific_price = $specific_price->id;
                    }
                } elseif ($reduction <= 0) {
                    $specific_price->reduction = 0;
                    if (!$specific_price->save()) {
                        $module = Module::getInstanceByName('roja45quotationspro');
                        throw new Exception(
                            sprintf(
                                $module->l('Unable to create cart price for product %s : %s'),
                                $product['id_product'],
                                Db::getInstance()->getMsgError()
                            )
                        );
                    }
                    if ($currency->id == $id_currency) {
                        $quotationProduct->id_specific_price = $specific_price->id;
                    }
                } else {
                    $quotationProduct->id_specific_price = 0;
                    $specific_price->reduction = 0;
                }
            }

            $id_customization = 0;
            if ($product['id_customization']) {
                $id_customization = QuotationCustomization::createCartCustomization(
                    $context->cart->id,
                    $product['qty'],
                    $product['id_customization'],
                    $context->cart->id_address_delivery
                );
            }

            $productObj = new Product(
                $product['id_product'],
                false,
                Configuration::get('PS_LANG_DEFAULT'),
                $context->shop->id
            );
            if (!$productObj->available_for_order || (Configuration::get('PS_CATALOG_MODE') && !defined('_PS_ADMIN_DIR_'))) {
                $module = Module::getInstanceByName('roja45quotationspro');
                throw new Exception(
                    sprintf(
                        $module->l('Product unavailable for ordering: [%s].'),
                        $product['product_title']
                    )
                );
            }

            $update_quantity = $context->cart->updateQty(
                $product['qty'],
                $product['id_product'],
                $product['id_product_attribute'],
                $id_customization,
                Tools::getValue('op', 'up'),
                null
            );
            if ($update_quantity < 0) {
                $module = Module::getInstanceByName('roja45quotationspro');
                throw new Exception(
                    sprintf(
                        $module->l('You are ordering less than the minimum quantity for item [%s].'),
                        $product['product_title']
                    )
                );
            }
            if (!$update_quantity) {
                $module = Module::getInstanceByName('roja45quotationspro');
                throw new Exception($module->l(
                    'An item in your quotation is unavailable for order, please contact support.'
                ));
            }

            if (!$quotationProduct->save()) {
                $module = Module::getInstanceByName('roja45quotationspro');
                throw new Exception($module->l(
                    'Unable to save quotation product, please try again.'
                ));
            }
        }

        $delivery_option = $this->id_carrier . ',';
        if ($delivery_option !== false) {
            $context->cart->setDeliveryOption(
                array(
                    $context->cart->id_address_delivery => $delivery_option,
                )
            );
            $context->cart->save();
        }

        $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
        foreach ($discounts as $discount) {
            if ($discount['id_cart_rule']) {
                $cart_rule = new CartRule($discount['id_cart_rule']);
                $cart_rule->delete();
            }
            $discountObj = new QuotationCharge($discount['id_roja45_quotation_charge']);
            $cart_discount = new CartRule();
            $cart_discount->quantity = 1;
            $cart_discount->quantity_per_user = 1;

            $cart_discount->reduction_tax = true;
            switch ($discount['charge_method']) {
                case QuotationCharge::$PERCENTAGE:
                    $cart_discount->reduction_percent = $discount['charge_value'];
                    break;
                case QuotationCharge::$VALUE:
                    $cart_discount->reduction_amount = $discount['charge_amount_wt'];
                    break;
            }
            $start_date = date('Y-m-d H:i:s');
            $cart_discount->date_from = $start_date;

            // CONFIGURATION option for quotation expiration
            $end_date = date('Y-m-d H:i:s', strtotime('+1 week'));
            $cart_discount->date_to = $end_date;
            $cart_discount->description = $discount['charge_name'];
            $gen_pass = Tools::strtoupper(RojaFortyFiveQuotationsProCore::passwdGen(8));
            $vouchercode = $this->reference;
            $name_v = $vouchercode . '-' . $gen_pass;

            $languages = Language::getLanguages(true);
            $namelang = array();
            foreach ($languages as $language) {
                $namelang[$language['id_lang']] = $name_v;
            }
            $cart_discount->name = $namelang;
            $cart_discount->id_customer = $this->id_customer;
            $code_v = $vouchercode . '-' . $gen_pass;
            $cart_discount->code = $code_v;
            $cart_discount->active = 1;
            $cart_discount->cart_rule_restriction = 0;
            $cart_discount->highlight = 1;
            //$cart_discount->reduction_currency = $id_currency;
            $defaultCurrency = Currency::getDefaultCurrency();
            $cart_discount->reduction_currency = $defaultCurrency->id;

            if (!$cart_discount->save()) {
                $context->cart->delete();
                $module = Module::getInstanceByName('roja45quotationspro');
                throw new Exception(
                    $module->l(
                        'Unable to save cart rule. If the problem persists, please contact your system administrator.'
                    )
                );
            }
            $discountObj->id_cart_rule = $cart_discount->id;
            if (!$discountObj->save()) {
                $context->cart->delete();
                $module = Module::getInstanceByName('roja45quotationspro');
                throw new Exception(
                    $module->l('Unable to save quotation discount, please try again.')
                );
            }
            $context->cart->addCartRule($cart_discount->id);
        }

        $this->id_cart = $context->cart->id;
        $this->modified = 0;

        if (!$this->save()) {
            return false;
        }
        return true;
    }

    public function generateQuotationPDF($display = false, $show_taxes = true)
    {
        $context = Context::getContext();
        $total_products = $this->getQuotationTotal(false, RojaQuotation::ONLY_PRODUCTS);
        $total_products_wt = $this->getQuotationTotal(true, RojaQuotation::ONLY_PRODUCTS);
        $currency = Currency::getCurrencyInstance($this->id_currency);
        $defaultCurrency = Currency::getDefaultCurrency();

        $discounts = $this->getQuotationChargeList(QuotationCharge::$DISCOUNT);
        foreach ($discounts as &$discount) {
            $discount['charge_value_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($discount['charge_value']),
                $currency
            );
            if ($discount['charge_method'] == QuotationCharge::$PERCENTAGE) {
                $discount['amount'] = $total_products * ((int) $discount['charge_value'] / 100);
                $discount['amount_wt'] = $total_products_wt * ((int) $discount['charge_value'] / 100);
            } elseif ($discount['charge_method'] == QuotationCharge::$VALUE) {
                $discount['amount'] = (double) $discount['charge_value'];
                $discount['amount_wt'] = (double) $discount['charge_value'];
            }

            $discount['amount_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($discount['amount']),
                $currency
            );
            $discount['amount_wt_formatted'] = RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice($discount['amount_wt']),
                $currency
            );
        }

        $custom_object = $this->getSummaryDetails($this->id_lang, $this->id_currency, $show_taxes);
        $custom_object['quotation_products'] = array_reverse($custom_object['quotation_products']);
        $quotation_request = new QuotationRequest($this->id_request);
        if ($quotation_request->form_data) {
            $requestJSON = json_decode($quotation_request->form_data);
        } else {
            $requestJSON = json_decode($this->form_data);
        }
        $request = array();
        $shipping_method = '';
        if (is_array($requestJSON)) {
            $requestJSON = $requestJSON[0];
        }
        if ($requestJSON) {
            $counter = 0;
            foreach ($requestJSON->columns as $column) {
                foreach ($column->fields as $field) {
                    if (($field->name != 'FIRSTNAME') &&
                        ($field->name != 'LASTNAME') &&
                        ($field->name != 'CONTACT_EMAIL')
                    ) {
                        $request[$counter]['name'] = $field->name;
                        $request[$counter]['value'] = isset($field->value) ? $field->value : 0;
                        $request[$counter]['id'] = isset($field->id) ? $field->id : 0;
                        $request[$counter]['label'] = $field->label;
                        if (isset($field->type) && ($field->type == 'CUSTOM_SELECT')) {
                        } elseif (isset($field->type) && ($field->type == 'SHIPPING_METHOD')) {
                            $carrier = new Carrier($request[$counter]['value'], $context->language->id);
                            $request[$counter]['value'] = $carrier->name;
                            $shipping_method = $carrier->name;
                        } elseif (isset($field->type) && ($field->type == 'STATE')) {
                            $state = new State($request[$counter]['id'], $context->language->id);
                            $request[$counter]['value'] = $state->name;
                        } elseif (isset($field->type) && ($field->type == 'COUNTRY')) {
                            $country = new Country($request[$counter]['id'], $context->language->id);
                            $request[$counter]['value'] = $country->name;
                        }
                        ++$counter;
                    }
                }
            }
        }

        $date = new DateTime();
        if (!$id_roja45_quotation_answer = (int) Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_PDF')) {
            throw new Exception('Customer quotation PDF template not selected in the configuration.');
        }
        $custom_object['id_roja45_quotation_answer'] = $id_roja45_quotation_answer;
        $custom_object['id_lang'] = $this->id_lang;
        $custom_object['filename'] = $this->reference . '.pdf';
        $custom_object['title'] = $this->reference;
        $custom_object['date'] = $date->format($context->language->date_format_lite);
        $custom_object['date_full'] = $date->format($context->language->date_format_full);
        $custom_object['header'] = Module::getInstanceByName('roja45quotationspro')->l('Quotation');
        $custom_object['notes'] = '';
        $custom_object['shipping_method'] = $shipping_method;
        $custom_object['use_taxes'] = $custom_object['show_taxes'];
        $custom_object['show_exchange_rate'] = ((float) $currency->conversion_rate == (float) 1.0) ? 0 : 1;
        $custom_object['exchange_rate'] = (float) $currency->conversion_rate;
        $custom_object['show_account'] = 0;
        $custom_object['show_product_customizations'] = $custom_object['quotation_has_customizations'];
        $custom_object['show_customization_cost'] = $custom_object['quotation_has_customization_cost'];
        $custom_object['show_ecotax'] = $custom_object['quotation_has_ecotax'];
        $custom_object['show_prices'] = (int) Configuration::get('ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY');
        $custom_object['show_product_discounts'] = $custom_object['quotation_has_discounts'];
        $custom_object['show_product_comments'] = $custom_object['quotation_has_comments'];
        $custom_object['show_additional_shipping'] = $custom_object['quotation_has_additional_shipping'];
        $custom_object['show_summary'] = 1;
        $language = new Language($this->id_lang);
        $custom_object['tax_text'] = $custom_object['show_taxes'] ?
        Module::getInstanceByName('roja45quotationspro')->l('inc.', false, RojaFortyFiveQuotationsProCore::getLocale($language)) :
        Module::getInstanceByName('roja45quotationspro')->l('exc.', false, RojaFortyFiveQuotationsProCore::getLocale($language));

        $pdf = new RojaPDF(array($custom_object), 'CustomPdf', Context::getContext()->smarty);
        $pdf->render($display);
        //return $pdf->render($display);
    }

    public static function deleteCustomerData($email)
    {
        $return = true;
        $id_customer = Customer::customerExists($email, true);
        if ($id_customer) {
            $quotations = RojaQuotation::getQuotationsForCustomer($id_customer);
            foreach ($quotations as $quotation) {
                $quotation = new RojaQuotation($quotation['id_roja45_quotation']);
                $return &= $quotation->delete();
            }
        }
        return $return;
    }

    public static function exportCustomerData($email)
    {
        $id_customer = Customer::customerExists($email, true);
        if ($id_customer) {
            $results = RojaQuotation::getQuotationsForCustomer($id_customer);
            $quotations = array();
            foreach ($results as $result) {
                $quotationObj = new RojaQuotation($result['id_roja45_quotation']);
                $quotation = array();
                $quotation['email'] = $email;
                $quotation['firstname'] = $quotationObj->firstname;
                $quotation['lastname'] = $quotationObj->lastname;
                $quotation['reference'] = $quotationObj->reference;
                $quotation['date_add'] = $quotationObj->date_add;
                $quotations[] = $quotation;
            }
            return json_encode($quotations);
        }
        return false;
    }

    public function isModified($id_cart, $id_currency)
    {
        $cart = new Cart($id_cart);
        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        $cart_products = (array) $cart->getProducts(true, false, null, false, false);
        if (!count($cart_products)) {
            return false;
        }
        $quotation_products = $this->getProducts();
        if (count($quotation_products) != count($cart_products)) {
            return true;
        }

        //$product_modified = false;
        foreach ($cart_products as $key => $cart_product) {
            $id_quotation_product = QuotationProduct::getQuotationProduct(
                $this->id_roja45_quotation,
                $cart_product['id_product'],
                $cart_product['id_product_attribute']
            );
            if ($id_quotation_product) {
                $quotation_product = new QuotationProduct($id_quotation_product);

                /*$specific_price = new SpecificPrice($quotation_product->id_specific_price);
                if (Validate::isLoadedObject($specific_price) && ($specific_price->id_currency != $id_currency)) {
                $product_modified = true;
                }*/

                if ($quotation_product->qty == $cart_product['cart_quantity']) {
                    unset($cart_products[$key]);
                }
            }
        }
        /*if ($product_modified) {
        return true;
        }*/

        if (count($cart_products)) {
            return true;
        } else {
            return false;
        }
    }

    public function addCarrierCharge($id_carrier, $cart)
    {
        $carrier = new Carrier($id_carrier);

        if ($this->id_carrier == $carrier->id) {
            throw new Exception('Carrier already assigned to this quotation.');
        }

        $products = $this->getProducts();
        $country = new Country($this->id_country);
        $id_zone = (int) $country->id_zone;

        $address = Address::initialize(null);
        $carrier_tax = $carrier->getTaxesRate($address);

        if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
            $shipping_cost = $carrier->getDeliveryPriceByWeight(
                $cart->getTotalWeight($products),
                $id_zone
            );
        } else {
            $order_total = $cart->getOrderTotal(
                true,
                Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
                $products
            );
            $shipping_cost = $carrier->getDeliveryPriceByPrice(
                $order_total,
                $id_zone,
                $this->id_currency
            );
        }

        // save shipping cost as new charge
        $charge = new QuotationCharge();
        $charge->id_roja45_quotation = $this->id;
        $charge->charge_name = $carrier->name;
        $charge->charge_default = true;
        $charge->charge_type = QuotationCharge::$SHIPPING;
        $charge_value = (float) $shipping_cost;
        $charge->charge_method = QuotationCharge::$VALUE;
        $charge->charge_amount = (float) Tools::ps_round(
            (float) $charge_value,
            (Currency::getCurrencyInstance((int) $this->id_currency)->decimals * _PS_PRICE_DISPLAY_PRECISION_)
        );

        $charge->charge_amount_wt = Tools::ps_round(
            $charge_value * (1 + ($carrier_tax / 100)),
            (Currency::getCurrencyInstance((int) $this->id_currency)->decimals * _PS_PRICE_DISPLAY_PRECISION_)
        );

        if ($carrier->shipping_handling && Configuration::get('PS_SHIPPING_HANDLING') > 0) {
            $charge->charge_handling = Configuration::get('PS_SHIPPING_HANDLING');
            $charge->charge_handling_wt = $charge->charge_handling * (1 + ($this->getTaxesAverage() / 100));
        }

        if (!$charge->save()) {
            throw new Exception('Unable to save charge.');
        }

        $this->id_carrier = (int) $id_carrier;
        if (!$this->save()) {
            throw new Exception('Unable to save quotation.');
        }
    }

    public static function searchProducts(
        $search,
        $product_category,
        $id_lang,
        $p = 1,
        $n = 50,
        $orderBy = 'id_product',
        $orderDir = 'ASC'
    ) {
        $sql = new DbQuery();
        $sql->select('COUNT(DISTINCT p.`id_product`)');
        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('product_attribute', 'pa', 'p.`id_product` = pa.`id_product`');
    
        if (!empty($search)) {
            $search = pSQL($search);
            $sql->where('(
                pl.`name` LIKE "%' . $search . '%"
                OR p.`reference` LIKE "%' . $search . '%"
                OR p.`ean13` LIKE "%' . $search . '%"
                OR p.`upc` LIKE "%' . $search . '%"
                OR pa.`reference` LIKE "%' . $search . '%"
                OR pa.`ean13` LIKE "%' . $search . '%"
                OR pa.`upc` LIKE "%' . $search . '%"
            )');
        }
    
        if ($product_category) {
            $sql->where('p.`id_product` IN (
                SELECT cp.id_product
                FROM `' . _DB_PREFIX_ . 'category_product` cp
                WHERE cp.id_category = ' . (int) $product_category . ')');
        }
        $total_results = Db::getInstance()->getValue($sql);

        if ($total_results) {
            $sql = new DbQuery();
            $sql->select(
                'p.`id_product`, pl.`name`, p.`ean13`, product_shop.`active`, p.`reference`,
                pl.`link_rewrite`, p.`supplier_reference`, p.`minimal_quantity`, m.`name` AS manufacturer_name, stock.`quantity`,
                product_shop.`wholesale_price`, product_shop.advanced_stock_management, p.`customizable`,
                GROUP_CONCAT(DISTINCT pa.`id_product_attribute` ORDER BY pa.`id_product_attribute` SEPARATOR ",") AS combination_ids,
                GROUP_CONCAT(DISTINCT pa.`reference` ORDER BY pa.`id_product_attribute` SEPARATOR ",") AS combination_references,
                GROUP_CONCAT(DISTINCT pa.`ean13` ORDER BY pa.`id_product_attribute` SEPARATOR ",") AS combination_ean13s,
                GROUP_CONCAT(DISTINCT pa.`upc` ORDER BY pa.`id_product_attribute` SEPARATOR ",") AS combination_upcs,
                CASE
                    WHEN pl.`name` LIKE "%' . $search . '%"
                        OR p.`reference` LIKE "%' . $search . '%"
                        OR p.`ean13` LIKE "%' . $search . '%"
                        OR p.`upc` LIKE "%' . $search . '%"
                    THEN "product"
                    ELSE "combination"
                END AS match_source'
            );
            $sql->from('product', 'p');
            $sql->join(Shop::addSqlAssociation('product', 'p'));
            $sql->leftJoin(
                'product_lang',
                'pl',
                'p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl')
            );
            $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
            $sql->leftJoin('product_attribute', 'pa', 'p.`id_product` = pa.`id_product`');
    
            if (!empty($search)) {
                $search = pSQL($search);
                $sql->where('(
                    pl.`name` LIKE "%' . $search . '%"
                    OR p.`reference` LIKE "%' . $search . '%"
                    OR p.`ean13` LIKE "%' . $search . '%"
                    OR p.`upc` LIKE "%' . $search . '%"
                    OR pa.`reference` LIKE "%' . $search . '%"
                    OR pa.`ean13` LIKE "%' . $search . '%"
                    OR pa.`upc` LIKE "%' . $search . '%"
                )');
            }

            if ($product_category) {
                $sql->where('p.`id_product` IN (
                SELECT cp.id_product
                FROM `' . _DB_PREFIX_ . 'category_product` cp
                WHERE cp.id_category = ' . (int) $product_category . ')');
            }

            $sql->join(Product::sqlStock('p', 0));
            $sql->groupBy('p.`id_product`');
            $sql->limit($n, ($p - 1) * $n);

            if ($orderBy) {
                $sql->orderBy($orderBy . ' ' . $orderDir);
            }

            $products = Db::getInstance()->executeS($sql);

            return array(
                'total_results' => $total_results,
                'pages' => (int) ceil($total_results / $n),
                'products' => $products,
                'search' => $search,
            );
        } else {
            return false;
        }
    }

    public static function generateReference()
    {
        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE')) {
            $format = Configuration::get('ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT');

            $reference = $format;
            if (Tools::strpos($format, '[sequential') !== false) {
                $pos = Tools::strpos($format, '[sequential');
                $len = (int) rtrim(Tools::substr($format, $pos + 12), ']');
                $sql = new DbQuery();
                $sql->select('id_roja45_quotation');
                $sql->from('roja45_quotationspro');
                $sql->orderBy('id_roja45_quotation DESC');
                if (!$id_roja45_quotation = (int) Db::getInstance()->getValue($sql)) {
                    $id_roja45_quotation = 1;
                }
                $string = str_pad($id_roja45_quotation, $len, '0', STR_PAD_LEFT);
                $reference = Tools::str_replace_once('[sequential ' . $len . ']', $string, $reference);
            }

            if (Tools::strpos($format, '[random') !== false) {
                $pos = Tools::strpos($format, '[random');
                $len = (int) rtrim(Tools::substr($format, $pos + 8), ']');
                $string = RojaFortyFiveQuotationsProCore::referenceGen($len);
                $reference = Tools::str_replace_once('[random ' . $len . ']', $string, $reference);
            }

            if (Tools::strpos($format, '[day]') !== false) {
                $date = new DateTime();
                $string = $date->format('d');

                $reference = Tools::str_replace_once('[day]', $string, $reference);
            }

            if (Tools::strpos($format, '[month]') !== false) {
                $date = new DateTime();
                $string = $date->format('m');

                $reference = Tools::str_replace_once('[month]', $string, $reference);
            }

            if (Tools::strpos($format, '[year]') !== false) {
                $date = new DateTime();
                $string = $date->format('y');

                $reference = Tools::str_replace_once('[year]', $string, $reference);
            }

            return $reference;
        } else {
            return Tools::strtoupper(RojaFortyFiveQuotationsProCore::referenceGen(9, 'NO_NUMERIC'));
        }
    }

    public function getFormData()
    {
        $form_fields = array();
        if ($form_data = json_decode($this->form_data)) {
            foreach ($form_data as $data) {
                $form_fields[$data->name] = $data->value;
            }
        }
        return $form_fields;
    }
}
