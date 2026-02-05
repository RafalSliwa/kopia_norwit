<?php

use Leasenow\Payment\Notification;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;

/**
 * Class LeaseNowNotificationModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 * @property bool display_footer
 * @property bool display_header
 */
class LeaseNowNotificationModuleFrontController extends ModuleFrontController
{

    /**
     * @var string
     */
    const CONTACT_TYPE_PHONE = 'PHONE';

    /**
     * @var string
     */
    const CONTACT_TYPE_EMAIL = 'EMAIL';

    /**
     * Initialize controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->display_footer = false;
        $this->display_header = false;

        parent::init();
    }

    /**
     * @throws PrestaShopException
     * @throws Exception
     */
    public function process()
    {

        include_once(_PS_MODULE_DIR_ . 'leasenow/libraries/Payment-core/vendor/autoload.php');

        $credentials = $this->module->getCredentials();

        if (!$credentials) {

            echo Notification::formatResponse(Notification::NS_ERROR);
            exit();
        }

        $notification = new Notification(
            $credentials['storeId'],
            $credentials['secret']
        );

        $result_check_request_notification = $notification->checkRequest();

        if (is_int($result_check_request_notification)) {
            echo Notification::formatResponse(Notification::NS_ERROR);
            exit();
        }

        $reservation_id = $result_check_request_notification['reservationId'];

        if (!Notification::isSupportedStatus($result_check_request_notification['status'])) {

            echo Notification::formatResponse(Notification::NS_OK, $reservation_id);
            exit();
        }

        $orderId = $this->findOrder($reservation_id);

        if ($orderId) {

            switch ($result_check_request_notification['status']) {
                case Notification::LEASING_STATUS_SETTLED:
                    $newStatus = _PS_OS_PAYMENT_;
                    break;
                case Notification::LEASING_STATUS_DECLINED:
                    $newStatus = _PS_OS_ERROR_;
                    break;
                case Notification::LEASING_STATUS_FILLED:
                    echo Notification::formatResponse(Notification::NS_ERROR, $reservation_id);
                    exit();
                default:
                    $newStatus = Configuration::get('PAYMENT_LEASENOW_NEW_STATUS');
                    break;
            }

            try {
                $history = new OrderHistory();
                $history->id_order = $orderId;
                $history->changeIdOrderState($newStatus, $orderId);
            } catch(Exception $e) {

                PrestaShopLogger::addLog('ING Leasenow error: ' . $e->getMessage());

                echo Notification::formatResponse(Notification::NS_ERROR, $reservation_id);
                exit();
            }

            echo Notification::formatResponse(Notification::NS_OK, $reservation_id);
            exit();
        }

        $email = '';
        $phone = '';

        foreach ($result_check_request_notification['customerData']->contacts as $contact) {

            switch ($contact->type) {
                case self::CONTACT_TYPE_PHONE:

                    if (isset($contact->prefix) && $contact->prefix) {
                        $phone = $contact->prefix;
                    }
                    $phone .= $contact->value;
                    break;
                case self::CONTACT_TYPE_EMAIL:
                    if (isset($contact->prefix) && $contact->prefix) {

                        $email .= $contact->prefix;
                    }

                    $email .= $contact->value;
                    break;
                default:
                    break;
            }
        }

        $productList = [];
        foreach ($result_check_request_notification['products'] as $product) {

            if (empty($product['merchantProductId'])
                || empty($product['quantity'])) {

                echo Notification::formatResponse(Notification::NS_ERROR, $reservation_id);
                exit();
            }

            $productData = explode(',', $product['merchantProductId']);

            $a = [
                'id'       => $productData[0],
                'quantity' => $product['quantity'],
            ];

            if (isset($productData[1])) {
                $a['idAttribute'] = $productData[1];
            }

            $productList[] = $a;
        }

        if ($this->createOrder(
            $result_check_request_notification['customerData']->name,
            $result_check_request_notification['customerData']->lastName,
            $email,
            $result_check_request_notification['customerData']->billingAddress->streetString,
            $result_check_request_notification['customerData']->billingAddress->postCode,
            $result_check_request_notification['customerData']->billingAddress->city,
            $productList,
            $phone,
            $reservation_id
        )) {
            echo Notification::formatResponse(Notification::NS_OK, $reservation_id);
            exit();
        }

        echo Notification::formatResponse(Notification::NS_ERROR, $reservation_id);
        exit();
    }

    /**
     * @param string $reservationId
     *
     * @return false|string
     */
    private function findOrder($reservationId)
    {

        $tableName = _DB_PREFIX_ . 'leasenow';

        return Db::getInstance()->getValue("SELECT `" . $tableName . "`.`id_order` FROM `" . $tableName . "` WHERE  `" . $tableName . "`.`id_leasing` = '" . pSQL($reservationId) . "';");
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $street
     * @param string $postCode
     * @param string $city
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createOrder($firstname, $lastname, $email, $street, $postCode, $city, $productList, $phone, $reservationId)
    {

        $customer = $this->createCustomer($firstname, $lastname, $email);

        if (!$customer) {
            return false;
        }

        $address = $this->createAddress($customer, $street, $postCode, $city, $phone);

        if (!$address) {
            return false;
        }

        $cart = $this->createCart($customer, $address);

        if (!$cart) {
            return false;
        }

        if (!$this->addProductsToCart($cart, $productList)) {
            return false;
        }

        return $this->createOrderByCart($cart, $reservationId);
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     *
     * @return Customer
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createCustomer($firstname, $lastname, $email)
    {

        $customer = new Customer();
        $lastnameAddress = pSQL($firstname);
        $firstnameAddress = pSQL($lastname);
        $customer->is_guest = 1;
        $customer->lastname = $lastnameAddress;
        $customer->firstname = $firstnameAddress;

        if ($this->module->isPs17()) {
            $crypto = new Hashing();

            $customer->passwd = $crypto->hash(
                '',
                _COOKIE_KEY_
            );
        }

        $customer->email = pSQL($email);
        $customer->add();

        return $customer;
    }

    /**
     * @param object $customer
     * @param string $street
     * @param string $postCode
     * @param string $city
     * @param string $phone
     *
     * @return Address
     */
    public function createAddress($customer, $street, $postCode, $city, $phone)
    {
        $address = new Address();
        $address->id_customer = $customer->id;
        $address->firstname = $customer->firstname;
        $address->lastname = $customer->lastname;
        $address->address1 = $street;
        $address->postcode = $postCode;
        $address->city = $city;
        $address->alias = $this->module->l('Alias', 'notification');
        $address->phone = $phone;

        $address->id_country = Country::getByIso('PL');
        $address->add();

        return $address;
    }

    /**
     * @param Customer $customer
     * @param Address  $address
     *
     * @return Cart
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createCart($customer, $address)
    {
        $cart = new Cart();
        $cart->id_customer = $customer->id;
        $cart->id_address_delivery = $address->id;
        $cart->id_address_invoice = $address->id;

        $cart->id_lang = Language::getIdByIso('PL');

        $cart->id_currency = Currency::getIdByIsoCode('PLN');
        $cart->secure_key = $customer->secure_key;
        $cart->add();

        $this->context->cart = $cart;

        return $cart;
    }

    /**
     * @param Cart  $cart
     * @param array $productList
     *
     * @return bool
     */
    public function addProductsToCart($cart, $productList)
    {

        if (!$productList) {
            return false;
        }

        foreach ($productList as $product) {

            $idAttribute = null;

            if (isset($product['idAttribute']) && $product['idAttribute']) {
                $idAttribute = $product['idAttribute'];
            }

            if (StockAvailable::getQuantityAvailableByProduct($product['id'], $idAttribute) < $product['quantity']) {
                return false;
            }

            if (!$cart->updateQty(
                $product['quantity'],
                $product['id'],
                $idAttribute
            )) {

                return false;
            }
        }

        return true;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function createOrderByCart($cart, $reservationId)
    {

        $valid = true;

        try {
            $this->module->validateOrder(
                $cart->id,
                Configuration::get('PAYMENT_LEASENOW_NEW_STATUS'),
                $cart->getOrderTotal(),
                $this->module->displayName,
                null,
                [],
                null,
                false,
                $cart->secure_key
            );

            Db::getInstance()->insert('leasenow', [
                'id_order'   => Order::getOrderByCartId($cart->id),
                'id_leasing' => pSQL($reservationId),
            ]);
        } catch(Exception $e) {
            $valid = false;
        }

        return $valid;
    }
}
