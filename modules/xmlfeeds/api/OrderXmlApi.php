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

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderXmlApi
{
    protected $feedId = 0;
    protected $settings = array();
    protected $fieldsName = array();
    protected $fieldsNameAdditionTable = array();
    protected $feedLangId = 1;
    protected $countryList = array();
    protected $stateList = array();
    protected $branchNames = array();
    protected $prefixStart = '';
    protected $prefixEnd = '';
    protected $currency = false;
    protected $totalProducts = 0;
    protected $orderLangId = 0;
    protected $combinationsCache = [];
    protected $currencies = [];
    protected $currentOrderCurrencyId = 0;

    public function getFeed($settings)
    {
        $this->settings = $settings;
        $this->feedLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->feedId = (int)$settings['id'];
        $this->currency = Currency::getCurrency(!empty($settings['currency_id']) ? $settings['currency_id'] : Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->branchNames = $this->getBranchNames();

        $this->loadAddressData();
        $orders = $this->getOrders();

        $currencies = Db::getInstance()->executeS('SELECT * 
            FROM `' . _DB_PREFIX_ . 'currency` c 
            ORDER BY `iso_code` ASC');

        foreach ($currencies as $c) {
            $this->currencies[$c['id_currency']] = $c;
        }

        return $this->generateXml($orders);
    }

    protected function generateXml($orders)
    {
        $xml = $this->getDeepTagName($this->branchNames['orders-branch-name']);

        if (!empty($this->settings['extra_feed_row'])) {
            $xml .= $this->settings['extra_feed_row'];
        }

        if (!empty($this->settings['cdata_status'])) {
            $this->prefixStart = '<![CDATA[';
            $this->prefixEnd = ']]>';
        }

        foreach ($orders as $order) {
            $this->currentOrderCurrencyId = $order['blmod_id_currency'];
            $xmlOrder = $this->getDeepTagName($this->branchNames['order-branch-name']);
            $productsBranch = $this->generateXmlAddProducts($order['order_id']);

            foreach ($order as $id => $val) {
                if (empty($this->fieldsName[$id])) {
                    continue;
                }

                if ($this->isPriceField($id)) {
                    $val = $this->getPriceFormat($val);
                }

                $xmlOrder .= $this->getDeepTagName($this->fieldsName[$id]).$this->prefixStart.$val.$this->prefixEnd.$this->getDeepTagName($this->fieldsName[$id], true);
            }

            foreach ($this->fieldsName as $fieldId => $fieldName) {
                if (strpos($fieldId, 'bl_extra_') !== 0) {
                    continue;
                }

                $method = 'extraField'.str_replace(' ', '', ucwords(str_replace(array('bl_extra_', '_'), array('', ' '), $fieldId)));
                $xmlOrder .= $this->getDeepTagName($fieldName).$this->$method($order).$this->getDeepTagName($fieldName, true);
            }

            $xml .= $this->replaceXmlTree($xmlOrder);

            $xml .= $productsBranch;
            $xml .= $this->getDeepTagName($this->branchNames['order-branch-name'], true);
        }

        $xml .= $this->getDeepTagName($this->branchNames['orders-branch-name'], true);

        return $xml;
    }

    protected function generateXmlAddProducts($orderId)
    {
        $xml = '';
        $this->totalProducts = 0;

        if (empty($this->fieldsNameAdditionTable['od'])) {
            return $xml;
        }

        $products = $this->getProducts($orderId);

        if (!empty($products)) {
            $this->totalProducts = count($products);
            $xml = $this->getDeepTagName($this->branchNames['products-branch-name']);

            foreach ($products as $product) {
                $xml .= $this->getDeepTagName($this->branchNames['product-branch-name']);
                $xmlOrder = '';

                foreach ($product as $id => $val) {
                    if ($id == 'blmod_product_id' || $id == 'blmod_product_attribute_id') {
                        continue;
                    }

                    if ($this->isPriceField($id)) {
                        $val = $this->getPriceFormat($val);
                    }

                    if ($this->isNumberField($id)) {
                        $val = $this->getNumberFormat($val);
                    }

                    if (!empty($this->orderLangId) && $id == 'order_detail_product_name') {
                        $combinations = $this->getAttributesResume($this->orderLangId, $product['blmod_product_id']);
                        $combinationName = '';

                        foreach ($combinations as $c) {
                            if ($c['id_product_attribute'] == $product['blmod_product_attribute_id']) {
                                $combinationName = ' ('.$c['attribute_designation'].')';
                                break;
                            }
                        }

                        $val = $val.$combinationName;
                    }

                    $xmlOrder .= $this->getDeepTagName($this->fieldsName[$id]).$this->prefixStart.$val.$this->prefixEnd.$this->getDeepTagName($this->fieldsName[$id], true);
                }

                $xml .= $this->replaceXmlTree($xmlOrder);
                $xml .= $this->getDeepTagName($this->branchNames['product-branch-name'], true);
            }

            $xml .= $this->getDeepTagName($this->branchNames['products-branch-name'], true);
        }

        return $xml;
    }

    protected function getOrders()
    {
        $field = $this->getFields();

        return Db::getInstance()->ExecuteS('SELECT DISTINCT(o.id_order) AS order_id, ad.address1, ad.address2, ad.postcode, ad.city, ad.id_country, ad.id_state,
            ad.company, ad.phone, ad.phone_mobile, ad.vat_number, ad.firstname AS firstname_d, ad.lastname AS lastname_d,
            ai.address1 AS address1_i, ai.address2 AS address2_i, ai.postcode AS postcode_i, ai.city AS city_i, 
            ai.id_country AS id_country_i, ai.id_state AS id_state_i,
            ai.company AS company_i, ai.phone AS phone_i, ai.phone_mobile AS phone_mobile_i, ai.vat_number AS vat_number_i, 
            ai.firstname AS firstname_i, ai.lastname AS lastname_i, o.total_paid_tax_excl AS total_paid_t_e,
            o.total_paid_tax_incl AS total_paid_t_i, o.id_currency AS blmod_id_currency'.pSQL($field).'        
            FROM '._DB_PREFIX_.'orders o
            LEFT JOIN '._DB_PREFIX_.'order_state_lang sl ON
            (sl.id_order_state = o.current_state AND sl.id_lang = "'.(int)$this->feedLangId.'")
            LEFT JOIN '._DB_PREFIX_.'customer c ON
            c.`id_customer` = o.`id_customer`
            LEFT JOIN '._DB_PREFIX_.'address ad ON
            ad.id_address = o.id_address_delivery
            LEFT JOIN '._DB_PREFIX_.'address ai ON
            ai.id_address = o.id_address_invoice
            LEFT JOIN '._DB_PREFIX_.'carrier cr ON
            cr.id_carrier = o.id_carrier'.$this->getOrdersFilter());
    }

    protected function getOrdersFilter()
    {
        $where = array();

        if (!empty($this->settings['order_state_status']) && !empty($this->settings['order_state'])) {
            $where[] = 'sl.id_order_state IN ('.pSQL($this->settings['order_state']).')';
        }

        if (!empty($this->settings['order_payments_status']) && !empty($this->settings['order_payment'])) {
            $where[] = 'o.module IN ("'.str_replace(',', '","', pSQL($this->settings['order_payment'])).'")';
        }

        if (!empty($this->settings['filter_date_type'])) {
            $dateToday = pSQL(date('Y-m-d').' 00:00:00');

            $filterByDate = array(
                OrderSettings::FILTER_DATE_NONE => '',
                OrderSettings::FILTER_DATE_TODAY => 'o.date_add >= "'.pSQL($dateToday).'"',
                OrderSettings::FILTER_DATE_YESTERDAY => 'o.date_add >= "'.pSQL(date('Y-m-d', strtotime('-1 day', strtotime($dateToday)))).' 00:00:00" AND o.date_add < "'.pSQL($dateToday).'"',
                OrderSettings::FILTER_DATE_THIS_WEEK => 'o.date_add >= "'.pSQL(date('Y-m-d', strtotime('this week', time()))).' 00:00:00"',
                OrderSettings::FILTER_DATE_THIS_MONTH => 'o.date_add >= "'.pSQL(date('Y-m-01')).' 00:00:00"',
                OrderSettings::FILTER_DATE_THIS_YEAR => 'o.date_add >= "'.pSQL(date('Y-01-01')).' 00:00:00"',
                OrderSettings::FILTER_DATE_CUSTOM_DAYS => 'o.date_add >= "'.pSQL(date('Y-m-d', strtotime('-'.(int)$this->settings['filter_custom_days'].' day', strtotime($dateToday)))).' 00:00:00"',
                OrderSettings::FILTER_DATE_DATE_RANGE => 'o.date_add >= "'.pSQL($this->settings['filter_date_from']).' 00:00:00" AND o.date_add <= "'.pSQL($this->settings['filter_date_to']).' 23:59:59" ',
            );

            $where[] = $filterByDate[$this->settings['filter_date_type']];
        }

        return !empty($where) ? ' WHERE '.implode(' AND ', $where) : '';
    }

    protected function getProducts($orderId)
    {
        if (!empty($this->orderLangId)) {
            $this->fieldsNameAdditionTable['od'] = str_replace('od.`product_name`', 'pl.name', $this->fieldsNameAdditionTable['od']);
        }

        return Db::getInstance()->ExecuteS('SELECT '.pSQL(trim($this->fieldsNameAdditionTable['od'], ',')).',
            od.product_id AS blmod_product_id, od.product_attribute_id AS blmod_product_attribute_id
            FROM '._DB_PREFIX_.'order_detail od
            LEFT JOIN '._DB_PREFIX_.'product p ON
            p.`id_product` = od.`product_id`
            LEFT JOIN '._DB_PREFIX_.'product_lang pl ON
            (pl.`id_product` = od.`product_id` AND pl.id_lang = '.(int)$this->orderLangId.')
            LEFT JOIN '._DB_PREFIX_.'order_detail_tax dt ON
            dt.id_order_detail = od.id_order_detail
            LEFT JOIN '._DB_PREFIX_.'tax t ON
            t.id_tax = dt.id_tax
            WHERE od.id_order = "'.(int)$orderId.'"
            ORDER BY od.id_order_detail ASC');
    }

    protected function getFields()
    {
        $fields = Db::getInstance()->ExecuteS('SELECT `name`, `status`, `title_xml`, `table`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$this->feedId.'" AND status = 1
            ORDER BY `table` ASC');

        $this->orderLangId = Db::getInstance()->getValue('SELECT `status`
            FROM '._DB_PREFIX_.'blmod_xml_fields
            WHERE category = "'.(int)$this->feedId.'" AND `name` = "order" AND `table` = "lang"');

        $field = '';
        $tableMap = [
            'orders' => 'o',
            'order_state_lang' => 'sl',
            'customer' => 'c',
            'order_detail' => 'od',
            'address' => 'ad',
            'product' => 'p',
            'tax' => 't',
            'carrier' => 'cr',
        ];

        if (!empty($fields)) {
            foreach ($fields as $f) {
                $this->fieldsName[$f['table'].'_'.$f['name']] = $f['title_xml'];

                if ($f['table'] == 'bl_extra') {
                    continue;
                }

                if (empty($tableMap[$f['table']])) {
                    continue;
                }

                $table = $tableMap[$f['table']];

                if ($f['table'] == 'order_detail' || $f['table'] == 'product' || $f['table'] == 'tax') {
                    if (empty($this->fieldsNameAdditionTable['od'])) {
                        $this->fieldsNameAdditionTable['od'] = '';
                    }

                    $this->fieldsNameAdditionTable['od'] .= $table.'.`'.$f['name'].'` AS '.$f['table'].'_'.$f['name'].',';
                    continue;
                }

                $field .= $table.'.`'.$f['name'].'` AS '.$f['table'].'_'.$f['name'].',';
            }

            if (empty($field)) {
                return '';
            }

            $field = ','.trim($field, ',');
        }

        return $field;
    }

    protected function extraFieldAddress($order, $prefix = '')
    {
        $final = array();
        $final[] = $order['address1'.$prefix];
        $final[] = $order['address2'.$prefix];
        $final[] = $order['postcode'.$prefix];
        $final[] = $order['city'.$prefix];
        $final[] = !empty($this->stateList[$order['id_country'.$prefix]][$order['id_state'.$prefix]]) ? $this->stateList[$order['id_country'.$prefix]][$order['id_state'.$prefix]] : '';
        $final[] = $order['postcode'.$prefix];

        return implode(' ', array_filter($final)).(!empty($this->countryList[$order['id_country'.$prefix]]) ? ', '.$this->countryList[$order['id_country'.$prefix]] : '');
    }

    protected function extraFieldDeliveryAddress($order)
    {
        $country = !empty($this->countryList[$order['id_country']]) ? $this->countryList[$order['id_country']] : '';

        return '<firstname>'.$this->prefixStart.$order['firstname_d'].$this->prefixEnd.'</firstname>
            <lastname>'.$this->prefixStart.$order['lastname_d'].$this->prefixEnd.'</lastname>
            <company>'.$this->prefixStart.$order['company'].$this->prefixEnd.'</company>
            <vat_number>'.$this->prefixStart.$order['vat_number'].$this->prefixEnd.'</vat_number>
            <address_line1>'.$this->prefixStart.$order['address1'].$this->prefixEnd.'</address_line1>
            <address_line2>'.$this->prefixStart.$order['address2'].$this->prefixEnd.'</address_line2>
            <post_code>'.$this->prefixStart.$order['postcode'].$this->prefixEnd.'</post_code>
            <city>'.$this->prefixStart.$order['city'].$this->prefixEnd.'</city>
            <country>'.$this->prefixStart.$country.$this->prefixEnd.'</country>
            <phone>'.$this->prefixStart.$order['phone'].$this->prefixEnd.'</phone>
            <phone_mobile>'.$this->prefixStart.$order['phone_mobile'].$this->prefixEnd.'</phone_mobile>';
    }

    protected function extraFieldInvoiceAddress($order)
    {
        $country = !empty($this->countryList[$order['id_country_i']]) ? $this->countryList[$order['id_country_i']] : '';

        return '<firstname>'.$this->prefixStart.$order['firstname_i'].$this->prefixEnd.'</firstname>
            <lastname>'.$this->prefixStart.$order['lastname_i'].$this->prefixEnd.'</lastname>
            <company>'.$this->prefixStart.$order['company_i'].$this->prefixEnd.'</company>
            <vat_number>'.$this->prefixStart.$order['vat_number_i'].$this->prefixEnd.'</vat_number>
            <address_line1>'.$this->prefixStart.$order['address1_i'].$this->prefixEnd.'</address_line1>
            <address_line2>'.$this->prefixStart.$order['address2_i'].$this->prefixEnd.'</address_line2>
            <post_code>'.$this->prefixStart.$order['postcode_i'].$this->prefixEnd.'</post_code>
            <city>'.$this->prefixStart.$order['city_i'].$this->prefixEnd.'</city>
            <country>'.$this->prefixStart.$country.$this->prefixEnd.'</country>
            <phone>'.$this->prefixStart.$order['phone_i'].$this->prefixEnd.'</phone>
            <phone_mobile>'.$this->prefixStart.$order['phone_mobile_i'].$this->prefixEnd.'</phone_mobile>';
    }

    protected function extraFieldCountry($order)
    {
        return (!empty($this->countryList[$order['id_country']]) ? $this->countryList[$order['id_country']] : '');
    }

    protected function extraFieldCity($order)
    {
        return !empty($order['city']) ? $order['city'] : '';
    }

    protected function extraFieldPostcode($order)
    {
        return !empty($order['postcode']) ?  $order['postcode'] : '';
    }

    protected function extraFieldCurrency($order)
    {
        $currencyIso = !empty($this->currencies[$this->currentOrderCurrencyId]['iso_code']) ? $this->currencies[$this->currentOrderCurrencyId]['iso_code'] : '';

        return !empty($this->currency['iso_code']) ? $currencyIso : '';
    }

    protected function extraFieldTaxTotalAmount($order)
    {
        return $this->getPriceFormat($order['total_paid_t_i'] - $order['total_paid_t_e']);
    }

    protected function extraFieldCustomerMessage($order)
    {
        return $this->getMessagesBranch($order['order_id']);
    }

    protected function extraFieldEmployeeMessage($order)
    {
        return $this->getMessagesBranch($order['order_id'], false);
    }

    protected function extraFieldVatNumberInvoice($order)
    {
        return !empty($order['vat_number_i']) ?  $order['vat_number_i'] : '';
    }

    protected function extraFieldTotalProducts($order)
    {
        if (!empty($this->totalProducts)) {
            return $this->totalProducts;
        }

        return Db::getInstance()->getValue('SELECT COUNt(od.id_order_detail)
            FROM '._DB_PREFIX_.'order_detail od
            WHERE od.id_order = '.(int)$order['order_id']);
    }

    protected function loadAddressData()
    {
        $countries = Db::getInstance()->ExecuteS('SELECT id_country, `name`
            FROM `'._DB_PREFIX_.'country_lang`
            WHERE `id_lang` = '.(int)$this->feedLangId);

        foreach ($countries as $c) {
            $this->countryList[$c['id_country']] = $c['name'];
        }

        $states = Db::getInstance()->ExecuteS('SELECT id_state, id_country, `name`
            FROM `'._DB_PREFIX_.'state`');

        foreach ($states as $s) {
            $this->stateList[$s['id_country']][$s['id_state']] = $s['name'];
        }
    }

    protected function getMessagesBranch($orderId, $isCustomerMessages = true)
    {
        $xml = '';

        $where = $isCustomerMessages ? '=' : '>';

        $messages = Db::getInstance()->ExecuteS('SELECT ct.id_customer_thread, cm.`message`, cm.`date_add`, cm.`private`
            FROM '._DB_PREFIX_.'customer_thread ct
            LEFT JOIN '._DB_PREFIX_.'customer_message cm ON
            cm.id_customer_thread = ct.id_customer_thread
            WHERE ct.id_order = '.(int)$orderId.' AND cm.`id_employee` '.pSQL($where).' 0
            ORDER BY cm.id_customer_message DESC');

        if (!empty($messages)) {
            foreach ($messages as $m) {
                $xml .= '<message>';
                $xml .= '<text>'.$this->prefixStart.$m['message'].$this->prefixEnd.'</text>';
                $xml .= '<date_add>'.$this->prefixStart.$m['date_add'].$this->prefixEnd.'</date_add>';
                $xml .= '<is_private>'.$this->prefixStart.$m['private'].$this->prefixEnd.'</is_private>';
                $xml .= '</message>';
            }
        }

        return $xml;
    }

    protected function getBranchNames()
    {
        $branchNamesKey = array();

        $branchNames = Db::getInstance()->ExecuteS('SELECT `name`, `value`, `category`
			FROM '._DB_PREFIX_.'blmod_xml_block
			WHERE category = "'.(int)$this->feedId.'"
		');

        foreach ($branchNames as $bl) {
            $branchNamesKey[$bl['name']] = isset($bl['value']) ? $bl['value'] : $bl['name'];
        }

        return $branchNamesKey;
    }

    protected function replaceXmlTree($xml)
    {
        preg_match_all("'<sBLMOD>(.*?)</sBLMOD>'si", $xml, $categories);

        $levels = array();

        if (empty($categories[1])) {
            return $xml;
        }

        foreach ($categories[1] as $k => $c) {
            preg_match("'<nBLMOD>(.*?)</nBLMOD>'si", $c, $name);
            $names = explode('_lBLMOD_', $name[1]);

            preg_match("'<vBLMOD>(.*?)</vBLMOD>'si", $c, $value);

            $levels[$names[0]][] = [
                'full' => $categories[0][$k],
                'name' => $names[1],
                'value' => $value[1],
            ];
        }

        foreach ($levels as $branchName => $branch) {
            $xmlN = '<'.$branchName.'>';
            $firstField = '';

            foreach ($branch as $b) {
                $xmlN .= '<'.$b['name'].'>';
                $xmlN .= $b['value'];
                $xmlN .= '</'.$b['name'].'>';

                if (empty($firstField)) {
                    $firstField = $b['full'];
                } else {
                    $xml = str_replace($b['full'], '', $xml);
                }
            }

            $xmlN .= '</'.$branchName.'>';

            $xml = str_replace($firstField, $xmlN, $xml);
        }

        return $xml;
    }

    protected function getDeepTagName($tag = '', $close = false)
    {
        if (empty($tag)) {
            return '';
        }

        if (strpos($tag, '/') === false) {
            return '<'.($close ? '/' : '').$tag.'>';
        }

        if ($close) {
            return '</vBLMOD></sBLMOD>';
        }

        return '<sBLMOD><nBLMOD>'.str_replace('/', '_lBLMOD_', $tag).'</nBLMOD><vBLMOD>';
    }

    protected function getPriceFormat($price = 0)
    {
        $currencyIso = !empty($this->currencies[$this->currentOrderCurrencyId]['iso_code']) ? $this->currencies[$this->currentOrderCurrencyId]['iso_code'] : '';

        if (!empty($this->settings['currency_id']) && $this->currentOrderCurrencyId != $this->settings['currency_id']) {
            $price = Tools::convertPrice($price, $this->settings['currency_id']);
            $currencyIso = !empty($this->currencies[$this->settings['currency_id']]['iso_code']) ? $this->currencies[$this->settings['currency_id']]['iso_code'] : '';
        }

        $price = PriceFormat::convertByType($price, $this->settings['price_format_id']);

        return $price.(!empty($this->settings['price_with_currency']) ? ' '.$currencyIso : '');
    }

    protected function getNumberFormat($number = 0)
    {
        return Tools::ps_round($number, 3);
    }

    protected function isPriceField($field)
    {
        $priceFields = array(
            'orders_total_paid',
            'orders_total_paid_tax_incl',
            'orders_total_paid_tax_excl',
            'orders_total_wrapping',
            'orders_total_discounts',
            'orders_total_products',
            'orders_total_shipping',
            'orders_total_shipping_tax_incl',
            'orders_total_shipping_tax_excl',
            'order_detail_total_price_tax_incl',
            'order_detail_total_price_tax_excl',
            'order_detail_unit_price_tax_incl',
            'order_detail_unit_price_tax_excl',
        );

        return in_array($field, $priceFields);
    }

    protected function isNumberField($field)
    {
        $fields = array(
            'tax_rate'
        );

        return in_array($field, $fields);
    }

    protected function getAttributesResume($id_lang, $productId, $attribute_value_separator = ': ', $attribute_separator = ', ')
    {
        if (!Combination::isFeatureActive()) {
            return [];
        }

        if (!empty($this->combinationsCache[$productId])) {
            return $this->combinationsCache[$productId];
        }

        $combinations = Db::getInstance()->executeS('SELECT pa.*, product_attribute_shop.*
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                WHERE pa.`id_product` = ' . (int) $productId . '
                GROUP BY pa.`id_product_attribute`
                ORDER BY pa.`id_product_attribute`');

        if (!$combinations) {
            return [];
        }

        $product_attributes = [];
        foreach ($combinations as $combination) {
            $product_attributes[] = (int) $combination['id_product_attribute'];
        }

        $lang = Db::getInstance()->executeS('SELECT pac.id_product_attribute, GROUP_CONCAT(agl.`name`, \'' . pSQL($attribute_value_separator) . '\',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \'' . pSQL($attribute_separator) . '\') as attribute_designation
                FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $id_lang . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $id_lang . ')
                WHERE pac.id_product_attribute IN (' . implode(',', $product_attributes) . ')
                GROUP BY pac.id_product_attribute
                ORDER BY pac.id_product_attribute');

        foreach ($lang as $k => $row) {
            $combinations[$k]['attribute_designation'] = $row['attribute_designation'];
        }

        $this->combinationsCache[$productId] = $combinations;

        return $combinations;
    }
}
