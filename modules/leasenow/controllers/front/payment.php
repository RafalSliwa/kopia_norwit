<?php

/**
 * Class LeaseNowPaymentModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class LeaseNowPaymentModuleFrontController extends ModuleFrontController
{

    /**
     * Initialize controller
     *
     * @throws PrestaShopException
     * @see FrontController::init()
     */
    public function init()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::init();
    }

    /**
     * @throws PrestaShopException
     * @throws Exception
     */
    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;

        $leasing = $this->context->cookie->__get('leasenowLeasing');

        if (!$cart->date_upd
            || !($this->context->cookie->__isset('leasenowLeasing') && $leasing)) {
            Tools::redirect('/');

            return;
        }

        $leasing = json_decode($leasing, true);

        $customer = new Customer($cart->id_customer);
        $cartId = $cart->id;

        $this->module->validateOrder($cartId,
            Configuration::get('PAYMENT_LEASENOW_NEW_STATUS'),
            $cart->getOrderTotal(),
            $this->module->displayName,
            null,
            [],
            null,
            false,
            $customer->secure_key
        );

        if (version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {

            $orderId = Order::getIdByCartId($cartId);
        } else {
            $orderId = Order::getOrderByCartId($cartId);
        }

        if (!(Db::getInstance()->insert('leasenow', [
            'id_order'   => $orderId,
            'id_leasing' => pSQL($leasing['reservationId']),
        ]))) {

            Tools::redirect('/');

            return;
        }

        $this->context->smarty->assign([
            'redirect_url'            => $leasing['redirectUrl'],
            'checkout_link'           => $this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE')
                ? 'order-opc'
                : 'order'),
            'text_return_to_checkout' => $this->module->l('Please wait, you will be returned to checkout.', 'payment'),
            'ga_key'                  => Configuration::get('LEASENOW_GA_KEY'),

            'loading_gif' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->module->name . '/views/img/loading.gif'),
        ]);

        $this->context->cookie->__set('leasenowLeasing', '');

        $this->setTemplate(LeaseNow::buildTemplatePath($this->module->isPs17()
            ? 'pay'
            : 'pay16', 'front'));
    }
}
