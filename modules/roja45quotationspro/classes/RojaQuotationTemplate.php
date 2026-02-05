<?php
/**
 * RojaQuotationTemplate.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaQuotationTemplate
 *
 * @link      https://toolecommerce.com/
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaQuotationTemplate.
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

class RojaQuotationTemplate extends ObjectModel
{
    public $id_roja45_quotation_template;
    public $id_lang;
    public $id_shop;
    public $id_currency;
    public $id_carrier;
    public $id_request;
    public $calculate_taxes;
    public $id_employee;
    public $template_name;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_template',
        'primary' => 'id_roja45_quotation_template',
        'multilang' => false,
        'fields' => array(
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'calculate_taxes' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'template_name' => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function delete()
    {
        // get all products, delete them
        $quotation_products = $this->getProducts();
        foreach ($quotation_products as $quotation_product) {
            $quotation_product = new RojaQuotationTemplateProduct(
                $quotation_product['id_roja45_quotation_template_product']
            );
            $quotation_product->delete();
        }

        $quotation_charges = $this->getCharges();
        foreach ($quotation_charges as $quotation_charge) {
            $quotation_charge = new RojaQuotationTemplateCharge(
                $quotation_charge['id_roja45_quotation_template_charge']
            );
            $quotation_charge->delete();
        }

        $quotation_discounts = $this->getDiscounts();
        foreach ($quotation_discounts as $quotation_discount) {
            $quotation_discount = new RojaQuotationTemplateCharge(
                $quotation_discount['id_roja45_quotation_template_charge']
            );
            $quotation_discount->delete();
        }

        return parent::delete();
    }

    public function getProducts($id_lang = null, $id_currency = null)
    {
        if (!$id_lang) {
            $id_lang = $this->id_lang;
        }
        if (!$id_currency) {
            $id_currency = $this->id_currency;
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_template_product', 'qp');
        $sql->leftJoin(
            'product',
            'p',
            'p.id_product = qp.id_product'
        );
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.id_product = pl.id_product AND pl.id_lang = ' . (int) $id_lang
        );
        $sql->where('id_roja45_quotation_template=' . (int) $this->id_roja45_quotation_template);

        $address = new Address();

        $currency = Currency::getCurrencyInstance($id_currency);
        $resultArray = array();
        if ($products = Db::getInstance()->executeS($sql)) {
            foreach ($products as &$product) {
                $productObj = new Product($product['id_product']);
                if (Validate::isLoadedObject($productObj)) {
                    $id_product_attribute = null;
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
                            $id_images = Product::getCover($product['id_product'], Context::getContext());
                            $id_image = $id_images['id_image'];
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
                        $id_images = Product::getCover($product['id_product'], Context::getContext());
                        $id_image = $id_images['id_image'];
                        $product['wholesale_price_formatted'] = Tools::displayPrice(
                            Tools::convertPrice(
                                $product['wholesale_price'],
                                $currency,
                                true
                            ),
                            $currency
                        );
                    }

                    $product['id_customization'] = null;
                    $product['id_address_invoice'] = null;
                    $product['id_address_delivery'] = null;
                    $product['quantity'] = $product['qty'];
                    $product['cart_quantity'] = $product['qty'];

                    $imageObj = new Image($id_image, (int) Context::getContext()->language->id);
                    if (!Validate::isLoadedObject($imageObj)) {
                        $id_image = Context::getContext()->language->iso_code . '-default';
                        $legend = 'No picture';
                    } else {
                        $legend = $imageObj->legend;
                    }

                    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
                        $format = RojaFortyFiveQuotationsProCore::getImageTypeFormattedName('cart');
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
                    $path = _PS_PROD_IMG_DIR_ . $imageObj->getImgPath() . '.' . $imageObj->image_format;
                    if (!file_exists($path)) {
                        $product['image_missing'] = 1;
                    }
                    $product['image_loc'] = $path;

                    $product['image_legend'] = $legend;
                    $product['image_width'] = $image_size['width'];
                    $product['image_height'] = $image_size['height'];

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
                        $this->id_currency,
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
                    $product['list_price_excl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['list_price_excl'],
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
                        $this->id_currency,
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
                    $product['list_price_incl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['list_price_incl'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $product['product_price_list_subtotal_excl'] = $product['list_price_excl'] * $product['qty'];
                    $product['product_price_list_subtotal_excl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_price_list_subtotal_excl'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $product['product_price_list_subtotal_incl'] = $product['list_price_incl'] * $product['qty'];
                    $product['product_price_list_subtotal_incl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_price_list_subtotal_incl'],
                            $currency,
                            true
                        ),
                        $currency
                    );

                    $product['product_price_list_tax'] = $product['list_price_incl'] - $product['list_price_excl'];
                    $product['product_price_list_tax_formatted'] = Tools::displayPrice(
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
                    $product['unit_price_tax_excl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['unit_price_tax_excl'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $product['product_price_formatted'] = $product['unit_price_tax_excl_formatted'];

                    $product['unit_price_tax_incl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['unit_price_tax_incl'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $product['product_price_inc_formatted'] = $product['unit_price_tax_incl_formatted'];

                    $product['product_price_subtotal_excl'] = $product['unit_price_tax_excl'] * $product['qty'];
                    $product['product_price_subtotal_excl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_price_subtotal_excl'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $product['product_price_subtotal_incl'] = $product['unit_price_tax_incl'] * $product['qty'];
                    $product['product_price_subtotal_incl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_price_subtotal_incl'],
                            $currency,
                            true
                        ),
                        $currency
                    );

                    $product['product_profit_subtotal_excl'] =
                        ($product['unit_price_tax_excl'] - $product['wholesale_price']) * $product['qty'];
                    $product['product_profit_subtotal_excl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_profit_subtotal_excl'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $product['product_profit_subtotal_incl'] =
                        ($product['unit_price_tax_incl'] - $product['wholesale_price']) * $product['qty'];
                    $product['product_profit_subtotal_incl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_profit_subtotal_incl'],
                            $currency,
                            true
                        ),
                        $currency
                    );

                    $product['tax_paid'] =
                        $product['product_price_subtotal_incl'] - $product['product_price_subtotal_excl'];
                    $product['tax_paid_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['tax_paid'],
                            $currency,
                            true
                        ),
                        $currency
                    );

                    $product['product_price_deposit_excl'] =
                        $product['product_price_subtotal_excl'] * ($product['deposit_amount'] / 100);
                    $product['product_price_deposit_excl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_price_deposit_excl'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $product['product_price_deposit_incl'] =
                        $product['product_price_subtotal_incl'] * ($product['deposit_amount'] / 100);
                    $product['product_price_deposit_incl_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $product['product_price_deposit_incl'],
                            $currency,
                            true
                        ),
                        $currency
                    );

                    $product['customizations'] = array();
                    $product['deleted'] = false;
                    $resultArray[$product['id_product'] . '-' . $product['id_product_attribute']] = $product;
                } else {
                    $product['deleted'] = true;
                    $resultArray[$product['id_product'] . '-' . $product['id_product_attribute']] = $product;
                }
            }
        }

        return $resultArray;
    }

    public function getCharges()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_template_charge');
        $sql->where('id_roja45_quotation_template=' . (int) $this->id_roja45_quotation_template);
        $sql->where('charge_type="' . pSQL(QuotationCharge::$CHARGE) . '"');
        if ($results = Db::getInstance()->executeS($sql)) {
            $currency = new Currency($this->id_currency);
            foreach ($results as &$row) {
                $row['charge_amount_formatted'] = Tools::displayPrice(
                    Tools::convertPrice(
                        $row['charge_amount'],
                        $currency,
                        true
                    ),
                    $currency
                );
                $row['charge_amount_wt_formatted'] = Tools::displayPrice(
                    Tools::convertPrice(
                        $row['charge_amount_wt'],
                        $currency,
                        true
                    ),
                    $currency
                );
            }
        }
        return $results;
    }

    public function getDiscounts()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_template_charge');
        $sql->where('id_roja45_quotation_template=' . (int) $this->id_roja45_quotation_template);
        $sql->where('charge_type="' . pSQL(QuotationCharge::$DISCOUNT) . '"');
        if ($results = Db::getInstance()->executeS($sql)) {
            $currency = new Currency($this->id_currency);
            foreach ($results as &$row) {
                if (isset($row['charge_amount'])) {
                    $row['discount_amount_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $row['charge_amount'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                    $row['discount_amount_wt_formatted'] = Tools::displayPrice(
                        Tools::convertPrice(
                            $row['charge_amount_wt'],
                            $currency,
                            true
                        ),
                        $currency
                    );
                }
            }
        }
        return $results;
    }

    /*
    public function addProduct(
    $id_product,
    $id_product_attribute,
    $retail_price,
    $qty = 1,
    $comment = null,
    $id_group = null,
    $customizations = array()
    ) {
    if (!$id_group) {
    $id_group = Configuration::get('PS_CUSTOMER_GROUP');
    }

    $address = $this->getTaxAddress();

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
    $id_product_attribute
    )) {
    $quotation_product = new QuotationProduct($id_quotation_product);
    $quotation_product->qty = $quotation_product->qty + $qty;
    $quotation_product->date_upd = date('Y-m-d H:i:s');
    } else {
    $quotation_product = new QuotationProduct();
    $quotation_product->id_roja45_quotation = (int)$this->id;
    $quotation_product->id_shop = (int)$this->id_shop;
    $quotation_product->id_lang = (int)$this->id_lang;
    $quotation_product->id_product = (int)$id_product;
    $quotation_product->id_product_attribute = (int)$id_product_attribute;
    $quotation_product->product_title = $product->name;
    $quotation_product->custom_price = false;
    $quotation_product->deposit_amount = 100;
    $quotation_product->qty = $qty;
    $quotation_product->date_add = date('Y-m-d H:i:s');
    $quotation_product->date_upd = date('Y-m-d H:i:s');
    }
    $null = null;

    $default_currency = Currency::getDefaultCurrency();

    if ($retail_price) {
    if ((int) $this->calculate_taxes) {
    $price_exc = Product::priceCalculation(
    $this->id_shop,
    $id_product,
    $id_product_attribute,
    $address->id_country,
    $address->id_state,
    $address->postcode,
    $this->id_currency,
    $id_group,
    1,
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
    1
    );

    $price_exc = $this->getPriceWithoutTax(
    $id_product,
    $retail_price,
    Context::getContext(),
    $address
    );

    $price_excl = Tools::ps_round($price_exc, 6);
    $price_incl = Tools::ps_round($retail_price, 6);
    } else {
    $price_inc = Product::priceCalculation(
    $this->id_shop,
    $id_product,
    $id_product_attribute,
    $address->id_country,
    $address->id_state,
    $address->postcode,
    $this->id_currency,
    $id_group,
    1,
    true,
    6,
    false,
    true,
    false,
    $null,
    true,
    0,
    true,
    null,
    1
    );

    $price_inc = $this->getPriceWithTax(
    $id_product,
    $retail_price,
    Context::getContext(),
    $address
    );

    $price_excl = Tools::ps_round($retail_price, 6);
    $price_incl = Tools::ps_round($price_inc, 6);
    }

    if ($retail_price != (float)$quotation_product->unit_price_tax_excl) {
    $quotation_product->custom_price = true;
    }
    } else {
    //$id_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
    $quotation_product->id_product_attribute = null;
    if ($combination) {
    $quotation_product->id_product_attribute = $combination->id;
    }
    $price_excl = Product::priceCalculation(
    $this->id_shop,
    (int)$product->id,
    $quotation_product->id_product_attribute,
    $address->id_country,
    $address->id_state,
    $address->postcode,
    $default_currency->id,
    $id_group,
    $qty,
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
    $qty
    );

    $price_incl = Product::priceCalculation(
    $this->id_shop,
    (int)$product->id,
    $quotation_product->id_product_attribute,
    $address->id_country,
    $address->id_state,
    $address->postcode,
    $default_currency->id,
    $id_group,
    $qty,
    true,
    6,
    false,
    true,
    false,
    $null,
    true,
    0,
    true,
    null,
    $qty
    );
    }

    $quotation_product->unit_price_tax_excl = (float)$price_excl;
    $quotation_product->unit_price_tax_incl = (float)$price_incl;

    $this->total_products += (float)$quotation_product->unit_price_tax_excl;
    $this->total_products_wt += (float)$quotation_product->unit_price_tax_incl;

    $quotation_product->comment = $comment;

    if ($quotation_product->save()) {
    foreach ($customizations as $customization) {
    $quotation_product->addCustomization($customization['id_roja45_quotation_customization']);
    }
    $this->save();
    return $quotation_product->id;
    } else {
    return 0;
    }
    }
     */

    public function getCarriers()
    {
        $_carriers = array();
        $country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        $products = $this->getProducts();
        $carriers = Carrier::getCarriers(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            true,
            false,
            (int) $country->id_zone,
            null,
            Carrier::ALL_CARRIERS
        );

        $total = $this->getQuotationTotal(true);
        foreach ($carriers as $k => $row) {
            /** @var Carrier $carrier */
            $_carriers[$row['id_carrier']]['carrier'] = new Carrier((int) $row['id_carrier']);
            $carrier = $_carriers[$row['id_carrier']]['carrier'];
            $shipping_method = $carrier->getShippingMethod();
            // Get only carriers that are compliant with shipping method
            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT &&
                $carrier->getMaxDeliveryPriceByWeight((int) $country->id_zone) === false
            ) || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE &&
                $carrier->getMaxDeliveryPriceByPrice((int) $country->id_zone) === false
            )
            ) {
                unset($carriers[$k]);
                continue;
            }

            // If out-of-range behavior carrier is set on "Desactivate carrier"
            if ($row['range_behavior']) {
                $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight(
                    $row['id_carrier'],
                    $this->getTotalWeight(),
                    (int) $country->id_zone
                );
                $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice(
                    $row['id_carrier'],
                    $total,
                    (int) $country->id_zone,
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
                    (int) $country->id_zone
                );
            } else {
                $shipping = $carrier->getDeliveryPriceByPrice(
                    $total,
                    (int) $country->id_zone,
                    (int) $this->id_currency
                );
            }

            $_carriers[$row['id_carrier']]['shipping'] = $shipping;
        }
        $return = array();
        $return['carriers'] = $_carriers;

        return $return;
    }

    public function getQuotationTotal($with_taxes = true)
    {
        $quotation_total = 0;
        $ecotax_total = 0;
        $products = $this->getProducts();
        foreach ($products as $product) {
            if ($with_taxes) {
                $price = (float) $product['unit_price_tax_incl'];
            } else {
                $price = (float) $product['unit_price_tax_excl'];
            }

            if (Configuration::get('PS_USE_ECOTAX')) {
                $ecotax = $product['ecotax'];
                if (isset($product['attribute_ecotax']) && $product['attribute_ecotax'] > 0) {
                    $ecotax = $product['attribute_ecotax'];
                }
            } else {
                $ecotax = 0;
            }

            if ($ecotax > 0) {
                $ecotax_total += $ecotax * (int) $product['qty'];
            }

            $quotation_total += ($price * (int) $product['qty']) + $ecotax_total;
        }

        if ($quotation_total < 0) {
            return 0;
        }

        return (float) $quotation_total;
    }

    public function getTotalWeight($products = null)
    {
        if (!is_null($products)) {
            $total_weight = 0;
            foreach ($products as $product) {
                if (!isset($product['weight_attribute']) || is_null($product['weight_attribute'])) {
                    $total_weight += $product['weight'] * $product['qty'];
                } else {
                    $total_weight += $product['weight_attribute'] * $product['qty'];
                }
            }
            return $total_weight;
        }

        if (Combination::isFeatureActive()) {
            $weight_product_with_attribute = Db::getInstance()->getValue('
            SELECT SUM((p.`weight` + pa.`weight`) * cp.`quantity`) as nb
            FROM `' . _DB_PREFIX_ . 'cart_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (cp.`id_product` = p.`id_product`)
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
            ON (cp.`id_product_attribute` = pa.`id_product_attribute`)
            WHERE (cp.`id_product_attribute` IS NOT NULL AND cp.`id_product_attribute` != 0)
            AND cp.`id_cart` = ' . (int) $this->id);
        } else {
            $weight_product_with_attribute = 0;
        }

        $weight_product_without_attribute = Db::getInstance()->getValue('
        SELECT SUM(p.`weight` * cp.`quantity`) as nb
        FROM `' . _DB_PREFIX_ . 'cart_product` cp
        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (cp.`id_product` = p.`id_product`)
        WHERE (cp.`id_product_attribute` IS NULL OR cp.`id_product_attribute` = 0)
        AND cp.`id_cart` = ' . (int) $this->id);

        $_totalWeight = round(
            (float) $weight_product_with_attribute + (float) $weight_product_without_attribute,
            3
        );

        return $_totalWeight;
    }

    /**
     * deleteProduct - Update a product line on a quotation.
     *
     * @return json
     *
     */
    public function deleteProduct($quotation_product)
    {
        //$this->total_products -= (float)$quotation_product->unit_price_tax_excl;
        //$this->total_products_wt -= (float)$quotation_product->unit_price_tax_incl;
        if (!$quotation_product->delete()) {
            return false;
        }

        if (!$this->save()) {
            return false;
        }

        return true;
    }

    private function calculateDiscount($quotation_total, $discounts)
    {
        $current_total = 0;
        foreach ($discounts as $discount) {
            // If the cart rule offers a reduction, the amount is prorated (with the products in the package)
            if ($discount['charge_method'] == QuotationCharge::$PERCENTAGE) {
                $current_total += $quotation_total * ((float) $discount['charge_value'] / 100);
            } elseif ($discount['charge_method'] == QuotationCharge::$VALUE) {
                $current_total += (float) $discount['charge_value'];
            }
        }
        return $current_total;
    }
}
