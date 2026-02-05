<?php
/**
 * QuotationRequest.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationRequest
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationRequest.
 *
 * @author    Roja45 <support@roja45.com>
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuotationRequest extends ObjectModel
{
    public $id_roja45_quotation_request;
    public $id_shop;
    public $id_currency;
    public $id_customer;
    public $id_guest;
    public $id_lang;
    public $date_add;
    public $date_upd;
    public $secure_key;
    public $form_data;
    public $requested;
    public $abandoned;
    public $reference;
    public $address_enable_auto_create;

    public $address_invoice_id;

    public $address_delivery_id;

    public static $instance = null;

    protected static $nbProducts = array();
    protected $products = null;
    protected static $totalWeight = array();
    protected $taxCalculationMethod = PS_TAX_EXC;
    protected static $customer = null;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_request',
        'primary' => 'id_roja45_quotation_request',
        'multilang' => false,
        'fields' => array(
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'required' => true,
            ),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'secure_key' => array('type' => self::TYPE_STRING, 'size' => 32),
            'form_data' => array('type' => self::TYPE_STRING),
            'requested' => array('type' => self::TYPE_BOOL),
            'abandoned' => array('type' => self::TYPE_BOOL),
            'reference' => array('type' => self::TYPE_STRING),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'lang' => false,
                'required' => true,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'lang' => false,
                'required' => true,
            ),
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);

        if ($this->id_customer) {
            if (isset(Context::getContext()->customer) && Context::getContext()->customer->id == $this->id_customer) {
                $customer = Context::getContext()->customer;
            } else {
                $customer = new Customer((int) $this->id_customer);
            }

            QuotationRequest::$customer = $customer;

            if ((!$this->secure_key || $this->secure_key == '-1') && $customer->secure_key) {
                $this->secure_key = $customer->secure_key;
                $this->save();
            }
        }

        if (!$this->address_enable_auto_create) {
            $this->address_enable_auto_create = (int) Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE');
        }

        $this->setTaxCalculationMethod();
    }

    public static function reset()
    {
        QuotationRequest::$instance = false;
    }

    public static function getInstance($create = false)
    {
        if (!QuotationRequest::$instance) {
            $instance = false;
            if ($id_roja45_quotation_request = Context::getContext()->cookie->__get(QuotationRequest::getCookieKey())) {
                $instance = new QuotationRequest($id_roja45_quotation_request);
                if (!Validate::isLoadedObject($instance)) {
                    Context::getContext()->cookie->__unset(QuotationRequest::getCookieKey());
                    $id_roja45_quotation_request = 0;
                }

                if ($instance->requested) {
                    Context::getContext()->cookie->__unset(QuotationRequest::getCookieKey());
                    $id_roja45_quotation_request = 0;
                }

                if (strtotime($instance->date_upd) > time() + 86400) {
                    Context::getContext()->cookie->__unset(QuotationRequest::getCookieKey());
                    $instance->abandoned = 1;
                    $instance->save();
                    $id_roja45_quotation_request = 0;
                }
            }

            if (!$id_shop = Context::getContext()->shop->id) {
                $id_shop = Configuration::get('PS_SHOP_DEFAULT');
            }
            
            if (!$id_roja45_quotation_request && $create) {
                $instance = new QuotationRequest();
                $instance->id_shop = $id_shop;
                $instance->date_add = date('Y-m-d H:i:s');
                $instance->id_lang = (int) Context::getContext()->language->id;
                $instance->id_currency = (int) Context::getContext()->currency->id;
                $instance->id_customer = (int) Context::getContext()->cart->id_customer;
                $instance->id_guest = (int) Context::getContext()->cart->id_guest;
                $instance->reference = RojaQuotation::generateReference();

                if ($instance->save()) {
                    Context::getContext()->cookie->__set(QuotationRequest::getCookieKey(), $instance->id);
                } else {
                    throw new Exception(Db::getInstance()->getMsgError());
                }
            }

            if ($instance && (
                    $instance->id_lang != (int) Context::getContext()->language->id ||
                    $instance->id_currency != (int) Context::getContext()->currency->id ||
                    $instance->id_customer != (int) Context::getContext()->cart->id_customer ||
                    $instance->id_guest != (int) Context::getContext()->cart->id_guest)) {
                $instance->id_lang = (int) Context::getContext()->language->id;
                $instance->id_shop = $id_shop;
                $instance->id_currency = (int) Context::getContext()->currency->id;
                $instance->id_customer = (int) Context::getContext()->cart->id_customer;
                $instance->id_guest = (int) Context::getContext()->cart->id_guest;
                $instance->save();
            }
            self::$instance = $instance;
        }
        return self::$instance;
    }

    public static function getCookieKey()
    {
        if (Context::getContext()->customer->isLogged()) {
            return 'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . Context::getContext()->cart->id_customer;
        } else {
            return 'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . Context::getContext()->cart->id_guest;
        }
    }

    public static function getCustomerCookieKey()
    {
        return 'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . Context::getContext()->cart->id_customer;
    }
    public static function getGuestCookieKey()
    {
        return 'ROJA45_QUOTATIONS_PRO_QUOTEREQUESTKEY_' . Context::getContext()->cart->id_guest;
    }
    public function nbProducts()
    {
        if (!$this->id) {
            return 0;
        }

        return Cart::getNbProducts($this->id);
    }

    public function getProducts(
        $refresh = false,
        $id_product = false
    ) {
        if (!$this->id) {
            return array();
        }
        // Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
        if ($this->products !== null && !$refresh) {
            // Return product row with specified ID if it exists
            if (is_int($id_product)) {
                foreach ($this->products as $product) {
                    if ($product['id_product'] == $id_product) {
                        return array($product);
                    }
                }
                return array();
            }
            return $this->products;
        }

        $sql = new DbQuery();

        $sql->select(
            'rp.`id_roja45_quotation_request`,rp.`id_roja45_quotation_requestproduct`,rp.`id_product_attribute`,
             rp.`id_product`,rp.`id_customization`,rp.`qty` AS quote_quantity,rp.id_shop,pl.`name`,p.`is_virtual`,
             pl.`description_short`, pl.`available_now`,pl.`available_later`,product_shop.`id_category_default`,
             p.`id_supplier`, p.`id_manufacturer`,product_shop.`on_sale`,product_shop.`ecotax`,
             product_shop.`additional_shipping_cost`, product_shop.`available_for_order`,product_shop.`price`,
             product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`,
             stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
             p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`,
             cl.`link_rewrite` AS category'
        );
        $sql->from('roja45_quotationspro_requestproduct', 'rp');
        $sql->leftJoin('product', 'p', 'p.`id_product` = rp.`id_product`');
        $sql->innerJoin(
            'product_shop',
            'product_shop',
            '(product_shop.`id_shop` = rp.`id_shop` AND product_shop.`id_product` = p.`id_product`)'
        );
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang(
                'pl',
                'rp.id_shop'
            )
        );

        $sql->leftJoin(
            'category_lang',
            'cl',
            'product_shop.`id_category_default` = cl.`id_category`
			AND cl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('cl', 'rp.id_shop')
        );

        $sql->leftJoin(
            'product_supplier',
            'ps',
            'ps.`id_product` = rp.`id_product`
            AND ps.`id_product_attribute` = rp.`id_product_attribute`
            AND ps.`id_supplier` = p.`id_supplier`'
        );

        // @todo test if everything is ok, then refactorise call of this method
        $sql->join(Product::sqlStock('rp', 'rp'));

        // Build WHERE clauses
        $sql->where('rp.`id_roja45_quotation_request` = ' . (int) $this->id);
        if ($id_product) {
            $sql->where('rp.`id_product` = ' . (int) $id_product);
        }
        $sql->where('p.`id_product` IS NOT NULL');

        // Build ORDER BY
        $sql->orderBy('rp.`date_add`, rp.`id_product`, rp.`id_product_attribute` ASC');

        if (Combination::isFeatureActive()) {
            $sql->select('
				product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
				IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
				(p.`weight`+ pa.`weight`) weight_attribute,
				IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
				IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
				IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
				IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price,
				product_shop.`wholesale_price`) wholesale_price
			');

            $sql->leftJoin(
                'product_attribute',
                'pa',
                'pa.`id_product_attribute` = rp.`id_product_attribute`'
            );
            $sql->leftJoin(
                'product_attribute_shop',
                'product_attribute_shop',
                '(product_attribute_shop.`id_shop` = rp.`id_shop`
                AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)'
            );
        } else {
            $sql->select(
                'p.`reference` AS reference, p.`ean13`,
				p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity,
				product_shop.`wholesale_price` wholesale_price'
            );
        }

        $sql->select('image.`id_image` id_image, il.`legend`');
        $sql->leftJoin(
            'image',
            'image',
            'image.`id_product` = p.`id_product` AND image.cover=1'
        );
        $sql->leftJoin(
            'image_lang',
            'il',
            'il.`id_image` = image.`id_image` AND il.`id_lang` = ' . (int) $this->id_lang
        );

        $products = Db::getInstance()->executeS($sql);

        $products_ids = array();
        $pa_ids = array();
        if ($products) {
            foreach ($products as $row) {
                $products_ids[] = $row['id_product'];
                $pa_ids[] = $row['id_product_attribute'];
            }
        } else {
            return array();
        }
        $this->products = array();

        $address = null;
        if ($this->id_customer) {
            $address = new Address(Address::getFirstCustomerAddressId(
                $this->id_customer,
                Context::getContext()->language->id
            ), Context::getContext()->language->id);
        } else {
            $address = new Address();
            $address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
            $address->id_state = 0;
        }

        $defaultCurrency = Currency::getDefaultCurrency();

        $currency = Currency::getCurrencyInstance($this->id_currency);
        foreach ($products as &$product) {
            $productObj = new Product($product['id_product']);
            if (Validate::isLoadedObject($productObj)) {
                //$id_product_attribute = null;
                if ($product['id_product_attribute'] > 0) {
                    //$id_product_attribute = $product['id_product_attribute'];
                    $combination = new Combination($product['id_product_attribute']);
                    $combo_str = '';
                    $combination_names = $combination->getAttributesName($this->id_lang);
                    foreach ($combination_names as $combination_name) {
                        $combo_str = $combo_str . ' [' . $combination_name['name'] . ']';
                    }
                    $product['product_title'] = $product['name'] . $combo_str;
                    $images = Image::getImages(
                        $this->id_lang,
                        $product['id_product'],
                        $product['id_product_attribute']
                    );
                    if (count($images)) {
                        $id_image = $images[0]['id_image'];
                    } else {
                        if ($id_images = Product::getCover($product['id_product'], Context::getContext())) {
                            $id_image = $id_images['id_image'];
                        } else {
                            $id_image = Context::getContext()->language->iso_code . '-default';
                        }
                    }
                    $product['wholesale_price'] = $combination->wholesale_price;
                    $product['wholesale_price_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['wholesale_price'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    if (!empty($combination->reference)) {
                        $product['reference'] = $combination->reference;
                    }
                } else {
                    if ($id_images = Product::getCover($product['id_product'], Context::getContext())) {
                        $id_image = $id_images['id_image'];
                    } else {
                        $id_image = Context::getContext()->language->iso_code . '-default';
                    }
                    $product['wholesale_price_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['wholesale_price'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                }

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
                $product['link'] = Context::getContext()->link->getProductLink(
                    $product['id_product'],
                    $product['link_rewrite'],
                    $product['category']
                );

                $imageObj = new Image($id_image, (int) Context::getContext()->language->id);
                if (!Validate::isLoadedObject($imageObj)) {
                    $id_image = Context::getContext()->language->iso_code . '-default';
                    $legend = 'No picture';
                } else {
                    $legend = $imageObj->legend;
                }

                if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                    $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('small');
                } else {
                    $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('medium');
                }
                $image_size = Image::getSize($format);

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

                $product['image_missing'] = 0;
                $path = $imageObj->getImgPath() . '.' . $imageObj->image_format;
                if (!file_exists(_PS_ROOT_DIR_ . _PS_IMG_ . $path)) {
                    $product['image_missing'] = 1;
                }
                $product['image_loc'] = _PS_ROOT_DIR_ . _PS_IMG_ . $path;

                // $product['image_element'] = QuotationAnswer::getImageTag(_PS_PROD_IMG_ . $path);

                $product['image_legend'] = $legend;
                $product['image_title'] = $legend;
                $product['image_width'] = $image_size['width'];
                $product['image_height'] = $image_size['height'];
                $product['image_quote'] = Context::getContext()->link->getImageLink(
                    $product['link_rewrite'],
                    $id_image,
                    RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('medium')
                );
                $product['image_quote_cart'] = Context::getContext()->link->getImageLink(
                    $product['link_rewrite'],
                    $id_image,
                    RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('cart')
                );

                if (isset($this->id_customer) && $this->id_customer) {
                    $id_group = (int) Customer::getDefaultGroupId($this->id_customer);
                } else {
                    $id_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
                }

                if ($address) {
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
                }

                if ($product['id_manufacturer']) {
                    $manufacturer = new Manufacturer(
                        $product['id_manufacturer'],
                        (int) Context::getContext()->language->id
                    );
                    $product['manufacturer'] = $manufacturer->name;
                    $product['manufacturer_logo'] = _PS_TMP_IMG_DIR_ .
                        'manufacturer_mini_' .
                        $product['id_manufacturer'] . '_' .
                        Context::getContext()->shop->id . '.jpg';
                }

                $product['qty_in_cart'] = Db::getInstance()->getValue(
                    'SELECT qty
                    FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct`
                    WHERE `id_roja45_quotation_request` = ' . (int) $product['id_roja45_quotation_request'] . '
                    AND `id_product` = ' . (int) $product['id_product'] . '
                    AND `id_product_attribute` = ' . (int) $product['id_product_attribute']
                );
                $product['cart_quantity'] = $product['qty_in_cart'];

                $specific_price = null;
                $productPriceInc = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    $product['qty_in_cart'],
                    true, /* USE TAX*/
                    6,
                    false,
                    true,
                    true, /* WITH ECOTAX */
                    $specific_price,
                    true,
                    0,
                    true,
                    null,
                    $product['qty_in_cart']
                );

                $productPriceIncWithoutReduction = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    $product['qty_in_cart'],
                    true, /* USE TAX*/
                    6,
                    false,
                    false,
                    true, /* WITH ECOTAX */
                    $specific_price,
                    true,
                    0,
                    true,
                    null,
                    $product['qty_in_cart']
                );

                $productPrice = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    $product['qty_in_cart'],
                    false, /* USE TAX*/
                    6,
                    false,
                    true,
                    true, /* WITH ECOTAX */
                    $specific_price,
                    true,
                    0,
                    true,
                    null,
                    $product['qty_in_cart']
                );

                $productPriceWithoutReduction = Product::priceCalculation(
                    $this->id_shop,
                    $product['id_product'],
                    $product['id_product_attribute'],
                    $address->id_country,
                    $address->id_state,
                    $address->postcode,
                    $defaultCurrency->id,
                    $id_group,
                    $product['qty_in_cart'],
                    false, /* USE TAX*/
                    6,
                    false,
                    false,
                    true, /* WITH ECOTAX */
                    $specific_price,
                    true,
                    0,
                    true,
                    null,
                    $product['qty_in_cart']
                );

                $product['product_price'] = $productPrice;
                $product['product_price_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($productPrice)
                );
                $product['product_price_inc'] = $productPriceInc;
                $product['product_price_inc_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($productPriceInc)
                );
                $product['product_price_without_reduction'] = $productPriceWithoutReduction;
                $product['product_price_without_reduction_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($productPriceWithoutReduction)
                );
                $product['product_price_without_reduction_inc'] = $productPriceIncWithoutReduction;
                $product['product_price_without_reduction_inc_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($productPriceIncWithoutReduction)
                );

                $product['product_price_reduction'] = $product['product_price_without_reduction'] - $product['product_price'];
                $product['product_price_reduction_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($product['product_price_without_reduction'] - $product['product_price'])
                );
                $product['product_price_reduction_inc'] = $product['product_price_without_reduction_inc'] - $product['product_price_inc'];
                $product['product_price_reduction_inc_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($product['product_price_without_reduction_inc'] - $product['product_price_inc'])
                );
                $product['product_price_currency_iso'] = Context::getContext()->currency->iso_code;
                $product['product_price_currency_symbol'] = Context::getContext()->currency->sign;

                $product['product_discounted'] = false;
                if ($productPrice < $productPriceWithoutReduction) {
                    $product['product_discounted'] = true;
                    $product['product_discount'] = Tools::ps_round(
                        (1 - ($productPrice / $productPriceWithoutReduction)) * 100,
                        2
                    );
                }

                $product['total_exc'] = $product['product_price'] * $product['quote_quantity'];
                $product['total_exc_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($product['total_exc'])
                );
                $product['total_inc'] = $product['product_price_inc'] * $product['quote_quantity'];
                $product['total_inc_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($product['total_inc'])
                );

                $product['total_tax'] = $product['total_inc'] - $product['total_exc'];
                $product['total_tax_formatted'] = Tools::displayPrice(
                    Tools::convertPrice($product['total_tax'])
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
                $this->products[] = $product;
            }
        }

        return $this->products;
    }

    public function updateQty(
        $quantity,
        $id_product,
        $id_product_attribute = null,
        $id_customization = null,
        $mode = 'up',
        Shop $shop = null
    ) {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        if (!$mode) {
            $mode = 'up';
        }
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->id);
        if (!Validate::isLoadedObject($product)) {
            throw new Exception('Unable to load product');
        }

        if ($id_product_attribute) {
            $combination = new Combination((int) $id_product_attribute);
            if ($combination->id_product != $id_product) {
                throw new Exception('Requested combination does not exist.');
            }
        }

        /* If we have a product combination, the minimal quantity is set with the one of this combination */
        if (!empty($id_product_attribute)) {
            if (version_compare(_PS_VERSION_, '8', '>=') == true) {
                $minimal_quantity = (int) ProductAttribute::getAttributeMinimalQty($id_product_attribute);
            } else {
                $minimal_quantity = (int) Attribute::getAttributeMinimalQty($id_product_attribute);
            }
        } else {
            $minimal_quantity = (int) $product->minimal_quantity;
        }

        if (isset(self::$nbProducts[$this->id])) {
            unset(self::$nbProducts[$this->id]);
        }

        if (isset(self::$totalWeight[$this->id])) {
            unset(self::$totalWeight[$this->id]);
        }

        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS') && !$product->available_for_order) {
            throw new Exception('Product unavailable for ordering.');
        }

        if ((int) $quantity <= 0) {
            return $this->deleteProduct(
                (int) $id_product,
                (int) $id_product_attribute,
                (int) $id_customization
            );
        } else {
            $result = $this->containsProduct(
                (int) $id_product,
                (int) $id_product_attribute,
                (int) $id_customization
            );

            if ($result) {
                if ($mode == 'up') {
                    $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
                        FROM ' . _DB_PREFIX_ . 'product p
                        ' . Product::sqlStock('p', (int) $id_product_attribute, true, $shop) . '
                        WHERE p.id_product = ' . (int) $id_product;

                    $result2 = Db::getInstance()->getRow($sql);
                    $product_qty = (int) $result2['quantity'];
                    $new_qty = (int) $result['qty'] + (int) $quantity;
                    //$qty = '+ '.(int)$quantity;
                    if (Configuration::get('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS')) {
                        if (!Product::isAvailableWhenOutOfStock((int) $result2['out_of_stock'])) {
                            if ($new_qty > $product_qty) {
                                throw new Exception(
                                    'Insufficient stock, you cannot add more of this product to your quote.'
                                );
                            }
                        }
                    }
                } elseif ($mode == 'down') {
                    //$qty = '- '.(int)$quantity;
                    if (Configuration::get('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS')) {
                        $new_qty = (int) $result['qty'] - (int) $quantity;
                        if ($new_qty < $minimal_quantity && $minimal_quantity > 1) {
                            throw new Exception('Insufficient quantity requested, unable to change quantity.');
                        }
                    }
                } else {
                    throw new Exception('No mode provided, unable to change quantity by requested amount.');
                }

                if ($new_qty <= 0) {
                    return $this->deleteProduct(
                        (int) $id_product,
                        (int) $id_product_attribute,
                        (int) $id_customization
                    );
                } elseif ($new_qty < $minimal_quantity) {
                    throw new Exception('New quantity is less than the minimum order quantity.');
                } else {
                    QuotationRequestProduct::updateQtyStatic((int) $this->id, $id_product, $id_product_attribute, $new_qty);
                }
            } elseif ($mode == 'up') {
                $sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
                    FROM ' . _DB_PREFIX_ . 'product p
                    ' . Product::sqlStock('p', (int) $id_product_attribute, true, $shop) . '
                    WHERE p.id_product = ' . (int) $id_product;

                $result2 = Db::getInstance()->getRow($sql);
                if (Configuration::get('ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS')) {
                    if (!Product::isAvailableWhenOutOfStock((int) $result2['out_of_stock'])) {
                        if ((int) $quantity > $result2['quantity']) {
                            throw new Exception('Product unavailable for ordering when out of stock.');
                        }
                    }
                    if ((int) $quantity < $minimal_quantity) {
                        throw new Exception('Insufficient quantity requested, unable to change quantity.');
                    }
                }

                $id_roja45_quotation_customization = 0;
                if ($id_customization) {
                    $id_roja45_quotation_customization = QuotationCustomization::createCustomization($id_customization);
                }
                $request_product = new QuotationRequestProduct();
                $request_product->id_product = (int) $id_product;
                $request_product->id_shop = (int) $shop->id;
                $request_product->id_product_attribute = (int) $id_product_attribute;
                $request_product->id_customization = (int) $id_roja45_quotation_customization;
                $request_product->id_roja45_quotation_request = (int) $this->id;
                $request_product->qty = (int) $quantity;
                $request_product->date_add = (int) $quantity;

                if (!$request_product->save()) {
                    throw new Exception('Unable to save the customer quotation cart.');
                }
            }
        }

        $this->products = $this->getProducts();
        if (!$this->update()) {
            $module = Module::getInstanceByName('roja45quotationspro');
            throw new Exception($module->l(
                'Unable to save quotation.',
                'QuotationsRequest'
            ));
        }

        return true;
    }

    public function getLastProductAdded()
    {
        // TODO - return last product added.
    }

    public function deleteRequestProduct($id_roja45_quotation_requestproduct)
    {
        $request_product = new QuotationRequestProduct($id_roja45_quotation_requestproduct);
        if ($request_product->delete()) {
            $return = $this->update();
            $this->products = $this->getProducts();
            return $return;
        }
        return false;
    }

    public function deleteProduct(
        $id_product,
        $id_product_attribute = null,
        $id_customization = null
    ) {
        if (isset(self::$nbProducts[$this->id])) {
            unset(self::$nbProducts[$this->id]);
        }

        if (isset(self::$totalWeight[$this->id])) {
            unset(self::$totalWeight[$this->id]);
        }

        /* Product deletion */
        $result = Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct`
            WHERE `id_product` = ' . (int) $id_product .
            (!is_null($id_product_attribute) ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute : '') . '
            AND `id_roja45_quotation_request` = ' . (int) $this->id . '
            AND id_customization = ' . (int) $id_customization
        );

        // TODO - delete things.
        // delete ps_customiztion
        if ($result) {
            $return = $this->update();
            $this->products = $this->getProducts();
            return $return;
        }

        return false;
    }

    public function delete()
    {
        $products = $this->getProducts();
        foreach ($products as $product) {
            $requestProduct = new QuotationRequestProduct(
                $product['id_roja45_quotation_requestproduct']
            );
            $requestProduct->delete();
        }

        return parent::delete();
    }

    public function deleteProducts()
    {
        /* Product deletion */
        foreach ($this->getProducts() as $product) {
            $this->deleteProduct(
                $product['id_product'],
                $product['id_product_attribute'],
                $product['id_customization']
            );
        }
    }

    public function containsProduct($id_product, $id_product_attribute = 0, $id_customization = 0)
    {
        $sql = new DbQuery();
        $sql->select('rp.qty');
        $sql->from('roja45_quotationspro_requestproduct', 'rp');
        $sql->where('rp.`id_product` = ' . (int) $id_product);
        $sql->where('rp.`id_product_attribute` = ' . (int) $id_product_attribute);
        $sql->where('rp.`id_customization` = ' . (int) $id_customization);
        $sql->where('rp.`id_roja45_quotation_request` = ' . (int) $this->id);
        $sql = $sql->build();
        $result = Db::getInstance()->getRow($sql);
        return $result;
    }

    public function update($null_values = false)
    {
        if (isset(self::$nbProducts[$this->id])) {
            unset(self::$nbProducts[$this->id]);
        }

        if (isset(self::$totalWeight[$this->id])) {
            unset(self::$totalWeight[$this->id]);
        }

        $this->products = null;
        $return = parent::update($null_values);
        return $return;
    }

    public function getSummaryDetails($id_lang = null, $id_currency = null)
    {
        $context = Context::getContext();
        if (!$id_lang) {
            $id_lang = $context->language->id;
        }

        if (!$id_currency) {
            $id_currency = $this->id_currency;
        }
        $currency = new Currency($id_currency);
        $language = new Language($this->id_lang);
        $products = $this->getProducts();
        $show_taxes = !Group::getPriceDisplayMethod(Group::getCurrent()->id);

        $has_customizations = false;
        $total_products_exc = 0;
        $total_products_inc = 0;
        $total_price_exc = 0;
        $total_price_inc = 0;
        $quotation_products_total = 0;
        foreach ($products as &$product) {
            if (isset($product['customizations']) && count($product['customizations'])) {
                $has_customizations = true;
            }
            $id_product_attribute = null;
            $id_image = 0;
            $product['quantity'] = $product['quote_quantity'];
            if ($product['id_product_attribute'] > 0) {
                $id_product_attribute = $product['id_product_attribute'];
                $combination = new Combination($product['id_product_attribute']);
                $combo_str = '';
                $combination_names = $combination->getAttributesName($id_lang);
                foreach ($combination_names as $combination_name) {
                    $combo_str = $combo_str . ' [' . $combination_name['name'] . ']';
                }
                $product['product_title'] = $product['name'] . $combo_str;


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
                $product['wholesale_price'] = $combination->wholesale_price;
                $product['wholesale_price_formatted'] = Tools::displayPrice(
                    Tools::convertPrice(
                        $product['wholesale_price'],
                        $currency,
                        true
                    ),
                    $currency
                );
                if (!empty($combination->reference)) {
                    $product['reference'] = $combination->reference;
                }
            } else {
                if ($id_images = Product::getCover($product['id_product'], Context::getContext())) {
                    $id_image = $id_images['id_image'];
                } else {
                    $id_image = Context::getContext()->language->iso_code . '-default';
                }
                $product['wholesale_price_formatted'] = Tools::displayPrice(
                    Tools::convertPrice(
                        $product['wholesale_price'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $product['product_title'] = $product['name'];
            }

            $total_products_exc += $product['total_exc'];
            $total_products_inc += $product['total_inc'];
            $total_price_exc += $product['total_exc'];
            $total_price_inc += $product['total_inc'];

            $product_list_price = $show_taxes ? $product['product_price_inc'] : $product['product_price'];
            $product['product_list_price'] = Tools::displayPrice(
                Tools::convertPrice($product_list_price)
            );
            $product_subtotal = $show_taxes ? $product['total_inc'] : $product['total_exc'];
            $product['product_subtotal'] = Tools::displayPrice(
                Tools::convertPrice($product_subtotal)
            );
            $product['product_tax'] = Tools::displayPrice(
                Tools::convertPrice($product['total_tax'])
            );
            $quotation_products_total += (int) $product['quote_quantity'];

            $imageObj = new Image($id_image, (int) Context::getContext()->language->id);
            if (!Validate::isLoadedObject($imageObj)) {
                $id_image = Context::getContext()->language->iso_code . '-default';
                $legend = 'No picture';
            } else {
                $legend = $imageObj->legend;
            }

            if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('small');
            } else {
                $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('medium');
            }
            $image_size = Image::getSize($format);

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

            $product['image_legend'] = $legend;
            $product['image_width'] = $image_size['width'];
            $product['image_height'] = $image_size['height'];
        }
        $total_tax = $total_price_inc - $total_price_exc;

        $request_data = array();

        $requestJSON = json_decode($this->form_data);
        if ($requestJSON) {
            $request = array();
            $counter = 0;
            foreach ($requestJSON->columns as $column) {
                foreach ($column->fields as $field) {
                    if (($field->name != 'FIRSTNAME') &&
                        ($field->name != 'LASTNAME') &&
                        ($field->name != 'CONTACT_EMAIL')
                    ) {
                        $request[$counter]['name'] = $field->name;
                        if (isset($field->value)) {
                            $request[$counter]['value'] = $field->value;
                        }
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
                        }
                        ++$counter;
                    }
                }
            }
            $request_data = array_merge(
                $request_data,
                array(
                    'request_data' => $requestJSON,
                    'form_data' => $request,
                )
            );
        }
        if ($this->id_customer) {
            $customer = new Customer($this->id_customer);
            $customer_title = '';
            if ($customer->id_gender) {
                $customer_title = new Gender($customer->id_gender, $context->language->id);
                $customer_title = $customer_title->name;
            }

            $id_address = Address::getFirstCustomerAddressId($this->id_customer, true);
            $customer_address = new Address($id_address);
            $customer_data = array(
                'customer_id' => $this->id_customer,
                'customer_title' => $customer_title,
                'customer_firstname' => $customer->firstname,
                'customer_lastname' => $customer->lastname,
                'customer_email' => $customer->email,
                'customer_address_id' => isset($customer_address->id) ? $customer_address->id : 0,
                'customer_address_address1' => !empty($customer_address->address1) ? $customer_address->address1 : '',
                'customer_address_address2' => !empty($customer_address->address2) ? $customer_address->address2 : '',
                'customer_company' => !empty($customer_address->company) ? $customer_address->company : '',
                'customer_address_city' => !empty($customer_address->city) ? $customer_address->city : '',
                'customer_address_postcode' => !empty($customer_address->postcode) ? $customer_address->postcode : '',
                'customer_address_country' => !empty($customer_address->country) ? $customer_address->country : '',
                'customer_phone' => !empty($customer_address->phone) ? $customer_address->phone : '',
                'customer_mobile' => !empty($customer_address->phone_mobile) ? $customer_address->phone_mobile : '',
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
                'customer_firstname' => 'Guest',
                'customer_lastname' => '',
                'customer_email' => '',
                'customer_phone' => '',
            );
        }
        $request_data = array_merge(
            $request_data,
            $customer_data
        );

        $today = new DateTime();
        $quote_valid_days = (int) Configuration::get('ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS');
        $valid_until = new DateTime();
        $valid_until->add(new DateInterval('P' . $quote_valid_days . 'D'));
        $quotation_data = array(
            'quotation_id' => $this->id,
            'quotation_reference' => $this->reference,
            'date' => $today->format($context->language->date_format_lite),
            'date_full' => $today->format($context->language->date_format_full),
            'quotation_date_created' => $this->date_add,
            'quotation_date_updated' => $this->date_upd,
            'quotation_has_customizations' => $has_customizations,
            'quotation_has_ecotax' => false,
            'quotation_has_discounts' => 0,
            'quotation_has_comments' => 0,
            'quotation_products' => array_values($products),
            'quotation_products_num' => count($products),
            'quotation_products_total' => $quotation_products_total,
            'language_id' => $language->id,
            'language_name' => $language->name,
            'language_iso_code' => $language->iso_code,
            'currency_id' => $currency->id,
            'currency_name' => $currency->name,
            'currency_iso_code' => $currency->iso_code,
            'currency_symbol' => RojaFortyFiveQuotationsProCore::getCurrencySymbol($currency),
            'show_taxes' => $show_taxes,
            'quotation_expiry_date' => $valid_until->format($context->language->date_format_lite),
            'quotation_expiry_time' => $valid_until->format('H:i'),
        );
        $request_data = array_merge(
            $request_data,
            $quotation_data
        );

        $totals_data = array(
            'total_products_exc_formatted' => Tools::displayPrice(
                Tools::convertPrice($total_products_exc)
            ),
            'total_products_inc_formatted' => Tools::displayPrice(
                Tools::convertPrice($total_products_inc)
            ),
            'total_price_exc_formatted' => Tools::displayPrice(
                Tools::convertPrice($total_price_exc)
            ),
            'total_price_inc_formatted' => Tools::displayPrice(
                Tools::convertPrice($total_price_inc)
            ),
            'total_tax_formatted' => Tools::displayPrice(
                Tools::convertPrice($total_tax)
            ),
            'quotation_subtotal' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_products_inc : $total_products_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_subtotal_exc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_products_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_subtotal_inc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_products_inc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_discounts' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    0,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_shipping' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    0,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_handling' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    0,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_tax' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    ($total_products_inc - $total_products_exc),
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_total' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $show_taxes ? $total_products_inc : $total_products_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_total_exc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_products_exc,
                    $currency,
                    true
                ),
                $currency
            ),
            'quotation_total_inc' => RojaFortyFiveQuotationsProCore::formatPrice(
                Tools::convertPrice(
                    $total_products_inc,
                    $currency,
                    true
                ),
                $currency
            ),
        );

        $request_data = array_merge(
            $request_data,
            $totals_data
        );

        return $request_data;
    }

    public function setTaxCalculationMethod()
    {
        $this->_taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
    }

    public function addProductCustomization(
        $id_product,
        $id_product_attribute,
        $id_customization,
        $id_shop = null
    ) {
        if ($id_roja45_quotation_requestproduct = QuotationRequestProduct::exists(
            $this->id,
            $id_product,
            $id_product_attribute,
            $id_shop
        )) {
            $requestproduct = new QuotationRequestProduct($id_roja45_quotation_requestproduct);
            $requestproduct->addCustomization($id_customization);
        }
    }

    public function hasShipping()
    {
        $form_data = json_decode($this->form_data);
        foreach ($form_data->columns as $column) {
            foreach ($column->fields as $field) {
                if ($field->type == 'SHIPPING_METHOD') {
                    return $field->id;
                }
            }
        }
        return Configuration::get('ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER');
    }

    public static function getRequestsForCustomer($id_customer)
    {
        $sql = new DbQuery();
        $sql->select('r.id_roja45_quotation_request');
        $sql->from('roja45_quotationspro_request', 'r');
        $sql->where('r.`id_customer` = ' . (int) $id_customer);
        return Db::getInstance()->executeS($sql);
    }

    public static function getNumberOfProducts($id_roja45_quotation_request)
    {
        if (isset(self::$nbProducts[$id_roja45_quotation_request]) &&
            self::$nbProducts[$id_roja45_quotation_request] !== null) {
            return self::$nbProducts[$id_roja45_quotation_request];
        }

        self::$nbProducts[$id_roja45_quotation_request] = (int) Db::getInstance()->getValue(
            'SELECT SUM(`qty`)
			FROM `' . _DB_PREFIX_ . 'roja45_quotationspro_requestproduct`
			WHERE `id_roja45_quotation_request` = ' . (int) $id_roja45_quotation_request
        );

        return self::$nbProducts[$id_roja45_quotation_request];
    }

    public function generatePDF($display = false, $show_taxes = true)
    {
        $answer = new QuotationAnswer(
            Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF'),
            $this->id_lang
        );
        return RojaPDF::generatePDF(
            'CustomPdf',
            $this,
            $display,
            array(
                'id_roja45_quotation_answer' => $answer->id,
            )
        );
    }

    public function generateNewAddress($data, $type_of_address) {
        $address = new Address();
        $address->id_customer = $this->id_customer;
        $address->alias = $type_of_address;
    
        $fieldMappings = [
            'ROJA45QUOTATIONSPRO_FIRSTNAME' => 'firstname',
            'ROJA45QUOTATIONSPRO_LASTNAME' => 'lastname',
            'ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS' => 'address1',
            'ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS2' => 'address2',
            'ROJA45QUOTATIONSPRO_CUSTOMER_CITY' => 'city',
            'ROJA45QUOTATIONSPRO_CUSTOMER_COUNTRY' => 'id_country',
            'ROJA45QUOTATIONSPRO_CUSTOMER_STATE' => 'id_state',
            'ROJA45QUOTATIONSPRO_CUSTOMER_ZIP' => 'postcode',
            'ROJA45QUOTATIONSPRO_CUSTOMER_PHONE' => 'phone',
            'ROJA45QUOTATIONSPRO_CUSTOMER_COMPANY' => 'company',
            'ROJA45QUOTATIONSPRO_CUSTOMER_DNI' => 'dni',
            'ROJA45QUOTATIONSPRO_CUSTOMER_VAT_NUMBER' => 'vat_number',
        ];
    
        foreach ($fieldMappings as $field => $property) {
            if (isset($data[$field])) {
                $address->$property = $data[$field];
            }
        }
    
        try {
            if ($address->save()) {
                $address->alias .= '_'.$address->id;
                $address->save();
            };
            return $address->id;
        } catch (Exception $e) {
            echo $e->getMessage();
            return 0;
        }
    }
    
    public function getFormData()
    {
        $form_data = json_decode($this->form_data);
        $form_fields = array();
        $counter = 0;
        $auto_address_data = array();
        $type_of_address = null;
        $field_address_invoice = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE');
        $field_address_delivery = Configuration::get('ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY');
        $customer_first_name = '';
        $customer_last_name = '';
        $counter_of_address_invoice = 0;
        $counter_of_address_delivery = 0;
        $is_reuse_for_address_invoice = 0;
        
        foreach ($form_data->columns as $column) {
            foreach ($column->fields as $field) {
                $form_fields[$counter]['name'] = $field->name;
                if (isset($field->value)) {
                    $form_fields[$counter]['value'] = $field->value;
                }
                $form_fields[$counter]['label'] = $field->label;
                if (isset($field->type) && ($field->type == 'CUSTOM_SELECT')) {
                } elseif (isset($field->type) && ($field->type == 'SHIPPING_METHOD')) {
                    if ($field->id) {
                        $form_fields[$counter]['value'] = $field->id;
                    }
                } elseif (isset($field->name) && ($field->name == 'ROJA45QUOTATIONSPRO_FIRSTNAME')) {
                    $customer_first_name = $field->value;
                } elseif (isset($field->name) && ($field->name == 'ROJA45QUOTATIONSPRO_LASTNAME')) {
                    $customer_last_name = $field->value;
                } elseif (isset($field->name) && ($field->name == 'ROJA45QUOTATIONSPRO_USE_THIS_ADDRESS_FOR_INVOICE')) {
                    $is_reuse_for_address_invoice = (int) $field->value;
                } elseif (isset($field->type) && ($field->type == 'ADDRESS_SELECTOR')) {
                    if ($field->id) {
                        $address = new Address($field->id);
                        $address = AddressFormat::generateAddress(
                            $address,
                            array(),
                            ', ',
                            ' ',
                            array()
                        );
                        $form_fields[$counter]['value'] = $address;
                        $form_fields[$counter]['id'] = $field->id;

                        if (stristr($field->name, "delivery") !== false) {
                            $this->address_delivery_id = $field->id;
                        }
                        
                        if (stristr($field->name, "invoice") !== false) {
                            $this->address_invoice_id = $field->id;
                        }
                    }

                    if ($this->address_enable_auto_create && ($field_address_invoice || $field_address_delivery)) {
                        if (stristr($field->name, "delivery") !== false) {
                            $type_of_address = $field_address_delivery;
                            $counter_of_address_delivery = $counter;
                        }

                        if (stristr($field->name, "invoice") !== false) {
                            $type_of_address = $field_address_invoice;
                            $counter_of_address_invoice = $counter;
                        }
                    }
                } else if ($type_of_address && strpos($field->name, "ROJA45QUOTATIONSPRO_CUSTOMER") === 0) {
                    $auto_address_data[$type_of_address][$field->name] = $field->type == 'COUNTRY' || $field->type == 'STATE' ? $field->id : $field->value;
                } elseif (isset($field->type) && in_array($field->type, ['COUNTRY', 'STATE']) && $field->id) {
                    $form_fields[$counter]['value'] = $field->id;
                }
                ++$counter;
            }
        }

        
        if ($this->address_enable_auto_create && $type_of_address && count($auto_address_data)) {
            if ($this->address_delivery_id) {
                $is_reuse_for_address_invoice = 0;
            }
            
            foreach ($auto_address_data as $key => $value) {
                $value['ROJA45QUOTATIONSPRO_FIRSTNAME'] = $customer_first_name;
                $value['ROJA45QUOTATIONSPRO_LASTNAME'] = $customer_last_name;
                $type_of_address = $key == $field_address_invoice ? 'Address_invoice' : 'Address_delivery';
                $new_address_id = $this->generateNewAddress($value, $type_of_address);
                $form_field_counter = null;

                if ($new_address_id) {
                    $address = new Address($new_address_id);
                    $address = AddressFormat::generateAddress(
                        $address,
                        array(),
                        ', ',
                        ' ',
                        array()
                    );

                    if (!$this->address_invoice_id && $key == $field_address_invoice && !$is_reuse_for_address_invoice) {
                        $this->address_invoice_id = $new_address_id;
                        $form_field_counter = $counter_of_address_invoice;
                    }

                    if (!$this->address_delivery_id && $key == $field_address_delivery) {
                        $this->address_delivery_id = $new_address_id;
                        $form_field_counter = $counter_of_address_delivery;

                        if (!$this->address_invoice_id && $is_reuse_for_address_invoice) {
                            $this->address_invoice_id = $new_address_id;
                            $form_fields[$counter_of_address_invoice]['id'] = $new_address_id;
                            $form_fields[$counter_of_address_invoice]['value'] = $address;
                        }
                    }

                    if ($form_field_counter && isset($form_fields[$form_field_counter])) {
                        $form_fields[$form_field_counter]['id'] = $new_address_id;
                        $form_fields[$form_field_counter]['value'] = $address;
                    }

                    if ($is_reuse_for_address_invoice) {
                        break;
                    }
                }
            }
        }
        return $form_fields;
    }
}
