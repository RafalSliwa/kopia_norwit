<?php
/**
 * 2007-2018 PrestaShop.
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoBs\Api\Client;
use CeneoBs\Service\AdditionalServiceProduct;

class ceneo_basketserviceDefaultModuleFrontController extends ModuleFrontController
{
    public $productServices;
    private $api;

    public function initContent()
    {
        parent::initContent();

        $secureKey = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
        $showOutput = Tools::getValue('show_output');

        if (empty($secureKey) || $secureKey !== Tools::getValue('token')) {
            exit('Invalid Source Key');
        }

        if (!Configuration::get('CENEO_BS_KEY')) {
            exit('Invalid Ceneo API Key');
        }

        $counter = 0;
        $this->api = new Client();
        $orders = $this->api->getAllOrders();

        if (isset($orders['d']) && isset($orders['d']['results']) && $orders = $orders['d']['results']) {
            $existingOrders = $this->checkExistingOrders();
            foreach ($orders as $order) {
                $ceneo_order_id = $order['Id'];

                if (!isset($existingOrders[$ceneo_order_id]) && (int) $order['OrderStateId'] == 30) {
                    $this->createOrder($ceneo_order_id, $order);
                    +$counter++;
                }
            }
        }

        if ($showOutput == '1') {
            echo 'Utworzono zamówień: ' . $counter;
        }

        exit;
    }

    public function checkExistingOrders(): array
    {
        $existing_orders = [];
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'ceneo_bs';
        $existing_orders_result = Db::getInstance()->executeS($sql);
        if ($existing_orders_result) {
            foreach ($existing_orders_result as $row) {
                $existing_orders[$row['ceneo_order_id']] = $row;
            }
        }
        return $existing_orders;
    }

    public function prepareName($string): string
    {
        $polishChars = [
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z',
            'Ą' => 'a', 'Ć' => 'c', 'Ę' => 'e', 'Ł' => 'l', 'Ń' => 'n', 'Ó' => 'o', 'Ś' => 's', 'Ź' => 'z', 'Ż' => 'z',
        ];
        $string = strtr($string, $polishChars);
        $string = str_replace(' ', '_', $string);
        return strtolower($string);
    }

    public function getOrderAdditional($order)
    {
        $additionalProducts = [];
        $data = $this->api->getOrderAdditionalServices($order);
        if (isset($data['d']) && isset($data['d']['results']) && is_array($data['d']['results'])) {
            $additionalProducts = $data['d']['results'];
        }

        return $additionalProducts;
    }


    public function createCeneoProducts($order)
    {
        $additionalProduct = $this->getOrderAdditional($order);
        if (!empty($additionalProduct)) {
            foreach($additionalProduct as $additional) {
                $this->productServices = new AdditionalServiceProduct(
                    'ceneo_' . $this->prepareName($additional['Name']),
                    $additional['Name'],
                    $additional['Price']
                );
                $this->productServices->createOrUpdateProduct();
            }
        }
    }

    public function createOrder($ceneo_order_id, $order)
    {
        $cart = new Cart();
        $shop_order = new Order();
        $customer = new Customer();
        $address_invoice = null;
        $shipping_result = $this->api->getShippingData($order);
        $invoice_result = $this->api->getInvoiceData($order);
        $payments_result = $this->api->getPaymentData($order);

        $this->createCeneoProducts($order);

        $payment = '';

        if (isset($payments_result['d'])) {
            $payment = trim($payments_result['d']['Value'] . ' ' . $payments_result['d']['Description']);
        }

        if (
            isset($shipping_result['d'])
            && isset($shipping_result['d']['results'])
            && $shipping = $shipping_result['d']['results'][0]
        ) {
            $email = $shipping['Email'];
            $firstname = $shipping['ShippingFirstName'];
            $lastname = $shipping['ShippingLastName'];
            $phone = $shipping['PhoneNumber'];
            $company = $shipping['ShippingCompanyName'];
            $address1 = $shipping['ShippingAddress'];
            $postcode = $shipping['ShippingPostCode'];
            $city = $shipping['ShippingCity'];

            $customer->email = $email;
            $customer->firstname = $firstname;
            $customer->lastname = $lastname;
            $randomPassword = Tools::passwdGen();
            $customer->passwd = Tools::hash($randomPassword);
            $customer->id_default_group = (int) Configuration::get('PS_GUEST_GROUP');

            try {
                $customer->add();
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $customer->addGroups([(int) Configuration::get('PS_GUEST_GROUP')]);

            $address = new Address();
            $address->id_customer = $customer->id;
            $address->phone = Validate::isPhoneNumber($phone) ? $phone : '000000000';
            $address->firstname = (Validate::isName($firstname) && !empty($firstname)) ? $firstname : 'Imię';
            $address->lastname = (Validate::isName($lastname) && !empty($lastname)) ? $lastname : 'Nazwisko';
            $address->company = $company;
            $address->address1 = empty($address1) ? 'Adres 1' : $address1;
            $address->postcode = $postcode;
            $address->city = $city;
            $address->id_country = Country::getByIso('PL');
            $address->alias = 'Adres dostawy';

            try {
                $address->add();
            } catch (Exception $e) {
                echo $e->getMessage();
                $customer->delete();
            }

            $cart->id_currency = (int) Currency::getIdByIsoCode('PLN');
            $cart->id_customer = $customer->id;
            $cart->date_add = date('Y-m-d H:i:s', time());
            $cart->id_shop_group = 1;
            $cart->id_shop = 1;

            try {
                $cart->add();
            } catch (Exception $e) {
                echo $e->getMessage();
                $address->delete();
                $customer->delete();
            }

            Context::getContext()->cart = $cart;
            Context::getContext()->currency = new Currency((int) Currency::getIdByIsoCode('PLN'));

            $details_result = $this->api->getOrderDetails($order);

            if (
                isset($details_result['d'])
                && isset($details_result['d']['results'])
                && $products = $details_result['d']['results']
            ) {
                foreach ($products as $product) {
                    $id_product = $product['ShopProductId'];

                    if (!Product::existsInDatabase($id_product, 'product')) {
                        echo 'Product ' . $product['Name'] . ' does not exist<br/>';
                        continue;
                    }

                    $quantity = $product['Count'];
                    try {
                        $cart->updateQty(
                            (int) $quantity,
                            (int) $id_product
                        );
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        $address->delete();
                        $cart->delete();
                        $customer->delete();
                        continue;
                    }
                }
            }

            if (
                isset($invoice_result['d'])
                && isset($invoice_result['d']['results'])
                && $invoice = $invoice_result['d']['results'][0]
            ) {
                if (isset($invoice['InvoiceAddress'])) {
                    $invoice_firstname = $invoice['InvoiceFirstName'];
                    $invoice_lastname = $invoice['InvoiceLastName'];
                    $invoice_phone = isset($invoice['PhoneNumber']) ? $invoice['PhoneNumber'] : '000000000';
                    $invoice_company = $invoice['InvoiceCompanyName'];
                    $invoice_address1 = $invoice['InvoiceAddress'];
                    $invoice_postcode = $invoice['InvoicePostCode'];
                    $invoice_city = $invoice['InvoiceCity'];
                    $invoice_country = $invoice['InvoiceCountry'];
                    $invoice_nip = $invoice['InvoiceNIP'];

                    $address_invoice = new Address();
                    $address_invoice->id_customer = $customer->id;
                    $address_invoice->phone = Validate::isPhoneNumber($invoice_phone) ? $invoice_phone : '000000000';
                    $address_invoice->firstname = (Validate::isName($invoice_firstname) && !empty($invoice_firstname)) ? $invoice_firstname : 'Firstname';
                    $address_invoice->lastname = (Validate::isName($invoice_lastname) && !empty($invoice_lastname)) ? $invoice_lastname : 'Nazwisko';
                    $address_invoice->company = $invoice_company;
                    $address_invoice->address1 = empty($invoice_address1) ? 'Adres 1' : $invoice_address1;
                    $address_invoice->postcode = $invoice_postcode;
                    $address_invoice->city = $invoice_city;
                    $address_invoice->vat_number = (Validate::isGenericName($invoice_nip) && !empty($invoice_nip)) ? $invoice_nip : '';
                    $address_invoice->id_country = Country::getByIso('PL');
                    $address_invoice->alias = 'Adres na fakturze';

                    try {
                        $address_invoice->add();
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        $address->delete();
                        $cart->delete();
                        $customer->delete();
                    }
                }
            }

            $customer = new Customer($cart->id_customer);
            $carrierId = $this->findCarrierIdFromNameInString(
                $order['ShopDeliveryFormName'],
                $this->api->getParcelCarriers()
            );

            $shop_order->id_cart = $cart->id;
            $shop_order->id_address_delivery = $address->id;
            $shop_order->id_address_invoice = $address_invoice ? $address_invoice->id : $address->id;
            $shop_order->id_currency = (int) Currency::getIdByIsoCode('PLN');
            $shop_order->reference = Order::generateReference();
            $shop_order->id_customer = $cart->id_customer;
            $shop_order->id_carrier = $carrierId;
            $shop_order->payment = $payment;
            $shop_order->module = 'ceneo_basketservice';
            $shop_order->total_paid = $order['OrderValue'];
            $shop_order->total_paid_real = $order['OrderValue'];
            $shop_order->total_paid_tax_incl = $order['OrderValue'];
            $shop_order->total_paid_tax_excl = $order['OrderValue'];
            $shop_order->total_products = $order['ProductsValue'];
            $shop_order->total_products_wt = $order['ProductsValue'];
            $shop_order->total_shipping = $order['DeliveryCost'];
            $shop_order->total_shipping_tax_excl = $order['DeliveryCost'];
            $shop_order->total_shipping_tax_incl = $order['DeliveryCost'];
            $shop_order->conversion_rate = 1;
            $shop_order->id_shop_group = 1;
            $shop_order->current_state = Configuration::get('CENEO_BS_STATE');
            $shop_order->id_shop = Context::getContext()->shop->id;
            $shop_order->secure_key = Db::getInstance()->getValue('SELECT secure_key FROM ' . _DB_PREFIX_ . 'customer 
            WHERE id_customer = ' . $cart->id_customer);

            try {
                try {
                    if (!$shop_order->add()) {
                        error_log('Order creation failed: ' . print_r($shop_order->getErrors(), true));
                        throw new Exception('Order creation failed.');
                    } else {
                        $orderCarrier = new OrderCarrier();
                        $orderCarrier->id_order = $shop_order->id;
                        $orderCarrier->id_carrier = $shop_order->id_carrier;
                        $orderCarrier->id_order_carrier = null; // automatyczne generowanie ID
                        $orderCarrier->shipping_cost_tax_incl = $shop_order->total_shipping_tax_incl;
                        $orderCarrier->shipping_cost_tax_excl = $shop_order->total_shipping_tax_excl;
                        $orderCarrier->date_add = date('Y-m-d H:i:s');

                        if (!$orderCarrier->add()) {
                            error_log('OrderCarrier creation failed: ' . print_r($orderCarrier->getErrors(), true));
                            throw new Exception('OrderCarrier creation failed.');
                        }
                    }
                } catch (Exception $e) {
                    error_log('Exception during order creation: ' . $e->getMessage());
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                $cart->delete();
                $address->delete();
                if ($address->invoice) {
                    $address_invoice->delete();
                }
                $customer->delete();
            }

            // Add custom additional product to order
            $additionalProduct = $this->getOrderAdditional($order);
            if (!empty($additionalProduct)) {
                foreach($additionalProduct as $additional) {
                    $this->productServices->addToCart($cart, 'ceneo_' . $this->prepareName($additional['Name']));
                }
            }

            try {
                $order_detail = new OrderDetail();
                $order_detail->createList($shop_order, $cart, 0, $cart->getProducts());
            } catch (Exception $e) {
                echo $e->getMessage();
                $shop_order->delete();
                $cart->delete();
                $address->delete();
                $address_invoice->delete();
                $customer->delete();
            }

            $parameters = [
                'OrderId' => $ceneo_order_id,
                'ShopOrderId' => $shop_order->reference,
            ];

            $parameters = urlencode('[' . json_encode($parameters) . ']');
            $order_reference_bs_result = $this->api->setOrders($parameters);

            $msg = new Message();
            if (!empty($order['CustomerRemark'])) {
                $msg->message = $order['CustomerRemark'];
                $msg->id_cart = $cart->id;
                $msg->id_customer = $customer->id;
                $msg->id_order = $shop_order->id;
                $msg->private = 0;
                try {
                    $msg->add();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $shop_order->delete();
                    $cart->delete();
                    $address->delete();
                    $address_invoice->delete();
                    $customer->delete();
                }
            }

            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'ceneo_bs VALUES (NULL, "' . $ceneo_order_id
                . '", ' . $shop_order->id
                . ', "' . date('Y-m-d H:i:s') . '", "' . $order['ShopDeliveryFormName']
                . '", "' . $payment . '")');
        }
    }

    private function findCarrierIdFromNameInString($string, $externalCarriers)
    {
        $prestashopCarriers = Carrier::getCarriers(Context::getContext()->language->id, true, false);
        $carrierNames = [];

        foreach ($prestashopCarriers as $carrier) {
            $carrierNames[strtolower($carrier['name'])] = $carrier['id_carrier'];
        }

        $courierNames = array_keys($carrierNames);
        $foundCourierName = $this->findCourierName($string, $courierNames);

        if ($foundCourierName !== null) {
            return $carrierNames[strtolower($foundCourierName)];
        }

        foreach (['inpost', 'Inpost', 'INPOST', 'inPost'] as $inpostVariant) {
            if (array_key_exists(strtolower($inpostVariant), $carrierNames)) {
                return $carrierNames[strtolower($inpostVariant)];
            }
        }

        return reset($carrierNames);
    }

    private function findCourierName($string, $courierNames)
    {
        foreach ($courierNames as $courierName) {
            if (stripos($string, $courierName) !== false) {
                return $courierName;
            }
        }
        return null;
    }
}
