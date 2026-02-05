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

/**
 * Documentation: https://help.readypro.it/it/1083/specifiche-xml-ordini
 */
class OrderXmlApiReadyPro
{
    protected $feedId = 0;
    protected $settings = array();
    protected $fieldsName = array();
    protected $fieldsNameAdditionTable = array();
    protected $feedLangId = 1;
    protected $countryList = array();
    protected $countryCodeList = array();
    protected $stateList = array();
    protected $stateIsoList = array();
    protected $branchNames = array();
    protected $prefixStart = '';
    protected $prefixEnd = '';
    protected $currency = false;
    protected $totalProducts = 0;
    protected $taxCode = '';
    protected $taxPercentAmount = '';
    protected $taxNameList = [];
    protected $isEinvoPrestaliaExists = false;

    public function getFeed($settings)
    {
        $this->settings = $settings;
        $this->feedLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->feedId = (int)$settings['id'];
        $this->currency = Currency::getCurrency(!empty($settings['currency_id']) ? $settings['currency_id'] : Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->branchNames = $this->getBranchNames();

        $this->setIsExistsEinvoPrestalia();

        $this->loadAddressData();

        $orders = $this->getOrders();

        return $this->generateXml($orders);
    }

    protected function generateXml($orders)
    {
        $this->loadTaxNames();

        $xml = '<ReadyFeed feedtype="WebOrder" version="1.0">';
        $xml .= '<WebOrders>';

        if (!empty($this->settings['extra_feed_row'])) {
            $xml .= $this->settings['extra_feed_row'];
        }

        if (!empty($this->settings['cdata_status'])) {
            $this->prefixStart = '<![CDATA[';
            $this->prefixEnd = ']]>';
        }

        foreach ($orders as $order) {
            $xmlOrder = '<WebOrder>';
            $xmlOrder .= '<DocDate>'.date('Y-m-d', strtotime($order['date_add'])).'</DocDate>';
            $xmlOrder .= '<SourceSite>'.Configuration::get('PS_SHOP_DOMAIN_SSL').'</SourceSite>';
            $xmlOrder .= '<OrderRef>'.$order['order_id'].'</OrderRef>';
            $xmlOrder .= $this->getCustomerBranch($order);
            $xmlOrder .= $this->getDestinationAddressBranch($order);
            $xmlOrder .= '<Payment>
                <Code>'.$order['reference'].'</Code>
                <Name>'.$order['payment'].'</Name>
                </Payment>';
            $xmlOrder .= '<DocRows>';
            $xmlOrder .= $this->getProductBranch($order);
            $xmlOrder .= $this->getShippingBranch($order);
            $xmlOrder .= $this->getPaymentExpenses();
            $xmlOrder .= '</DocRows>';
            $xmlOrder .= '<CurrencyType>
                    <Code>EUR</Code>
                </CurrencyType>';
            $xmlOrder .= '<InvoiceRequested>'.((!empty($order['vat_number'] || !empty($order['dni_i'])) ? 1 : 0) ).'</InvoiceRequested>';
            $xmlOrder .= '<SourceSiteTotal>'.$this->getNumberFormat($order['total_paid_t_i']).'</SourceSiteTotal>';
            $xmlOrder .= '<InsuranceAmount>0</InsuranceAmount>';
            $xmlOrder .= $this->getCourierService($order);
            $xmlOrder .= '</WebOrder>';

            $xml .= $xmlOrder;
        }

        $xml .= '</WebOrders>';

        return $xml.'</ReadyFeed>';
    }

    protected function getCustomerBranch($order)
    {
        $province = !empty($this->stateIsoList[$order['id_country_i']][$order['id_state_i']]) ? $this->stateIsoList[$order['id_country_i']][$order['id_state_i']] : '';

        return '<Anag>
                <Name>'.$this->prefixStart.$order['firstname_i'].' '.$order['lastname_i'].$this->prefixEnd.'</Name>
                <Address>'.$this->prefixStart.$order['address1_i'].' '.$order['address2_i'].$this->prefixEnd.'</Address>
                <ZipCode>'.$this->prefixStart.$order['postcode_i'].$this->prefixEnd.'</ZipCode>
                <City>'.$this->prefixStart.$order['city_i'].$this->prefixEnd.'</City>
                <Province>'.$this->prefixStart.$province.$this->prefixEnd.'</Province>
                <CountryCode>'.$this->prefixStart.$this->countryCodeList[$order['id_country']].$this->prefixEnd.'</CountryCode>
                <EmailAddress>'.$this->prefixStart.$order['email'].$this->prefixEnd.'</EmailAddress>
                <PhoneNumber>'.$this->prefixStart.$order['phone'].$this->prefixEnd.'</PhoneNumber>
                <VatNumber>'.$this->prefixStart.$order['vat_number'].$this->prefixEnd.'</VatNumber>
                <PersonalID>'.$this->prefixStart.$order['dni_i'].$this->prefixEnd.'</PersonalID>
                <EInvoiceAddress>'.$this->prefixStart.(!empty($order['pec_sdi']) ? $order['pec_sdi'] : '').$this->prefixEnd.'</EInvoiceAddress>
            </Anag>';
    }

    protected function getDestinationAddressBranch($order)
    {
        $province = !empty($this->stateIsoList[$order['id_country']][$order['id_state']]) ? $this->stateIsoList[$order['id_country']][$order['id_state']] : '';

        return '<DestinationAddress>
            <Name1>'.$this->prefixStart.$order['firstname_d'].' '.$order['lastname_d'].$this->prefixEnd.'</Name1>
            <Address1>'.$this->prefixStart.$order['address1'].' '.$order['address2'].$this->prefixEnd.'</Address1>
            <ZipCode>'.$this->prefixStart.$order['postcode'].$this->prefixEnd.'</ZipCode>
            <City>'.$this->prefixStart.$order['city'].$this->prefixEnd.'</City>
            <Province>'.$this->prefixStart.$province.$this->prefixEnd.'</Province>
            <CountryCode>'.$this->prefixStart.$this->countryCodeList[$order['id_country']].$this->prefixEnd.'</CountryCode>
        </DestinationAddress>';
    }

    protected function getProductBranch($order)
    {
        $xml = '';
        $products = $this->getProducts($order['order_id']);

        if (empty($products)) {
            return $xml;
        }

        $totalDiscountPrice = 0;
        $totalDiscountQty = 0;
        $notes = $this->getMessagesBranch($order['order_id']);

        foreach ($products as $p) {
            $this->taxCode = !empty($this->taxNameList[$p['id_tax']]) ? $this->taxNameList[$p['id_tax']] : '';

            $xml .= '<WebOrderRow>
                <RowType>Product</RowType>
                <Product>
                    <Code>'.$p['product_reference'].'</Code>
                </Product>
                <Quantity>'.$p['product_quantity'].'</Quantity>
                <Price>'.$this->getNumberFormat($p['unit_price_tax_excl']).'</Price>
                <PriceIncludingVAT>'.$this->getNumberFormat($p['unit_price_tax_incl']).'</PriceIncludingVAT>
                <DiscountPercent>'.$p['reduction_percent'].'</DiscountPercent>
                <CurrencyType>
                    <Code>EUR</Code>
                </CurrencyType>
                <Tax>
                    <Code>'.$this->taxCode.'</Code>
                    <PercentAmount>'.$p['tax_rate'].'</PercentAmount>
                </Tax>
                <Notes>'.$this->prefixStart.$notes.$this->prefixEnd.'</Notes>
            </WebOrderRow>';

            $notes = '';

            if ($p['reduction_percent'] > 0.01) {
                $priceWDiscount = $this->getNumberFormat($p['unit_price_tax_incl'] / (1 - $p['reduction_percent'] / 100));
                $totalDiscountPrice += ($priceWDiscount - $this->getNumberFormat($p['unit_price_tax_incl']));
                $totalDiscountQty = 1;          }


            $this->taxPercentAmount = $p['tax_rate'];
        }

        $xml .= '<WebOrderRow>
            <RowType>Discount</RowType>
            <Quantity>'.$totalDiscountQty.'</Quantity>
            <Price>'.$totalDiscountPrice.'</Price>
            <CurrencyType>
                <Code>EUR</Code>
            </CurrencyType>
            <Tax>
                <Code>'.$this->taxCode.'</Code>
                <PercentAmount>'.(!empty($p['tax_rate']) ? $p['tax_rate'] : '').'</PercentAmount>
            </Tax>
            </WebOrderRow>';

        return $xml;
    }

    protected function getShippingBranch($order)
    {
        return '<WebOrderRow>
            <RowType>ShippingExpenses</RowType>
            <Quantity>1</Quantity>
            <Price>'.$this->getNumberFormat($order['total_shipping_tax_incl']).'</Price>
            <CurrencyType>
                <Code>EUR</Code>
            </CurrencyType>
            <Tax>
                <Code>'.$this->taxCode.'</Code>
                <PercentAmount>'.$this->taxPercentAmount.'</PercentAmount> 
            </Tax>
            <Notes></Notes>
            </WebOrderRow>';
    }

    protected function getPaymentExpenses()
    {
        return '<WebOrderRow>
            <RowType>PaymentExpenses</RowType>
            <Quantity>0</Quantity>
            <PriceIncludingVAT>0</PriceIncludingVAT>
            <CurrencyType>
                <Code>EUR</Code>
            </CurrencyType>
            <Tax>
                <Code>'.$this->taxCode.'</Code>
                <PercentAmount>'.$this->taxPercentAmount.'</PercentAmount> 
            </Tax>
            <Notes></Notes>
            </WebOrderRow>';
    }

    protected function getCourierService($order)
    {
        return '<Courier>
                <Name>'.$this->prefixStart.$order['carrier_name'].$this->prefixEnd.'</Name>
            </Courier>
            <CourierService>
                <Name>'.$order['carrier_delay'].'</Name>
                <HoldForPickup>false</HoldForPickup>
            </CourierService>
            <ShippingNotes></ShippingNotes>
            <Notes></Notes>';
    }

    protected function getOrders()
    {
        $einvoPrestaliaDataSelect = '';
        $einvoPrestaliaDataJoin = '';

        if ($this->isEinvoPrestaliaExists) {
            $einvoPrestaliaDataSelect = ', ep.pec_sdi';
            $einvoPrestaliaDataJoin = 'LEFT JOIN ' . _DB_PREFIX_ . 'einvo_prestalia_data ep ON
                (ep.id_address = ad.id_address AND ep.id_shop = 1)';
        }

        return Db::getInstance()->ExecuteS('SELECT DISTINCT(o.id_order) AS order_id, ad.address1, ad.address2, ad.postcode, ad.city, ad.id_country, ad.id_state,
            ad.company, ad.phone, ad.phone_mobile, ad.vat_number, ad.firstname AS firstname_d, ad.lastname AS lastname_d,
            ai.address1 AS address1_i, ai.address2 AS address2_i, ai.postcode AS postcode_i, ai.city AS city_i, 
            ai.id_country AS id_country_i, ai.id_state AS id_state_i,
            ai.company AS company_i, ai.phone AS phone_i, ai.phone_mobile AS phone_mobile_i, ai.vat_number AS vat_number_i, 
            ai.firstname AS firstname_i, ai.lastname AS lastname_i, o.total_paid_tax_excl AS total_paid_t_e,
            o.total_paid_tax_incl AS total_paid_t_i, o.date_add, o.reference, c.email, ad.phone, cr.name AS carrier_name,
            o.total_shipping_tax_incl, ad.dni, ai.dni AS dni_i, o.payment, cl.delay AS carrier_delay'.$einvoPrestaliaDataSelect.'
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
            cr.id_carrier = o.id_carrier
            LEFT JOIN '._DB_PREFIX_.'carrier_lang cl ON
            (cl.id_carrier = o.id_carrier AND cl.id_shop = 1 AND cl.id_lang = '.(int)$this->feedLangId.')
            '.$einvoPrestaliaDataJoin.'  
            '.$this->getOrdersFilter());
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
        return Db::getInstance()->ExecuteS('SELECT od.product_id, od.product_quantity, od.total_price_tax_incl,
            od.total_price_tax_excl, od.unit_price_tax_incl, od.unit_price_tax_excl, od.product_reference,
            od.tax_name, od.reduction_percent, od.product_quantity_discount, t.rate AS tax_rate, t.id_tax
            FROM '._DB_PREFIX_.'order_detail od
            LEFT JOIN '._DB_PREFIX_.'product p ON
            p.`id_product` = od.`product_id`
            LEFT JOIN '._DB_PREFIX_.'order_detail_tax dt ON
            dt.id_order_detail = od.id_order_detail
            LEFT JOIN '._DB_PREFIX_.'tax t ON
            t.id_tax = dt.id_tax
            WHERE od.id_order = "'.(int)$orderId.'"
            ORDER BY od.id_order_detail ASC');
    }

    protected function loadAddressData()
    {
        $countries = Db::getInstance()->ExecuteS('SELECT l.id_country, l.`name`, c.iso_code
            FROM `'._DB_PREFIX_.'country_lang` l
            LEFT JOIN `'._DB_PREFIX_.'country` c ON
            c.id_country = l.id_country
            WHERE l.`id_lang` = '.(int)$this->feedLangId);

        foreach ($countries as $c) {
            $this->countryList[$c['id_country']] = $c['name'];
            $this->countryCodeList[$c['id_country']] = $c['iso_code'];
        }

        $states = Db::getInstance()->ExecuteS('SELECT id_state, id_country, `name`, iso_code
            FROM `'._DB_PREFIX_.'state`');

        foreach ($states as $s) {
            $this->stateList[$s['id_country']][$s['id_state']] = $s['name'];
            $this->stateIsoList[$s['id_country']][$s['id_state']] = $s['iso_code'];
        }
    }

    protected function loadTaxNames()
    {
        $taxes = Db::getInstance()->ExecuteS('SELECT l.id_tax, l.`name`
            FROM `'._DB_PREFIX_.'tax_lang` l
            WHERE l.`id_lang` = '.(int)$this->feedLangId);

        foreach ($taxes as $t) {
            $this->taxNameList[$t['id_tax']] = $t['name'];
        }
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

    protected function getPriceFormat($price = 0)
    {
        if (!empty($this->settings['currency_id'])) {
            $price = Tools::convertPrice($price, $this->settings['currency_id']);
        }

        $price = PriceFormat::convertByType($price, $this->settings['price_format_id']);

        return $price.(!empty($this->settings['price_with_currency']) ? ' '.$this->currency['iso_code'] : '');
    }

    protected function getMessagesBranch($orderId, $isCustomerMessages = true)
    {
        $where = $isCustomerMessages ? '=' : '>';

        return Db::getInstance()->getValue('SELECT cm.`message`
            FROM '._DB_PREFIX_.'customer_thread ct
            LEFT JOIN '._DB_PREFIX_.'customer_message cm ON
            cm.id_customer_thread = ct.id_customer_thread
            WHERE ct.id_order = '.(int)$orderId.' AND cm.`id_employee` '.pSQL($where).' 0
            ORDER BY cm.id_customer_message ASC');
    }

    protected function getNumberFormat($number = 0)
    {
        return Tools::ps_round($number, 2);
    }

    protected function setIsExistsEinvoPrestalia()
    {
        $name = Db::getInstance()->getValue('SELECT c.table_name
            FROM information_schema.tables c
            WHERE c.table_schema = "'.pSQL(_DB_NAME_).'" 
            AND c.table_name = "'.pSQL(_DB_PREFIX_.'einvo_prestalia_data').'"');

        $this->isEinvoPrestaliaExists = !empty($name);

        return $this->isEinvoPrestaliaExists;
    }
}
