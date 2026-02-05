<?php

/**
 * Class LeaseNowSuccessModuleFrontController
 *
 * @property bool display_column_left
 * @property bool display_column_right
 */
class LeaseNowSuccessModuleFrontController extends ModuleFrontController
{

    /**
     *  Initialize controller
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
     */
    public function initContent()
    {
        parent::initContent();

        $gaCartId = (int) Tools::getValue('ga_cart_id');
        $gaHash = Tools::getValue('ga_hash');
        $gaKey = Configuration::get('LEASENOW_GA_KEY');

        if (!$gaKey || $gaCartId <= 0) {
            $this->setTemplate(LeaseNow::buildTemplatePath('success', 'front'));

            return;
        }

        $this->context->smarty->assign('ga_key', $gaKey);

        try {
            $cart = new Cart($gaCartId);

            if (hash('sha256', $cart->id . $cart->secure_key) === $gaHash) {
                $currencyInfo = Currency::getCurrency($cart->id_currency);

                $gaConversion = [
                    [
                        'command'    => 'ecommerce:addTransaction',
                        'properties' => [
                            'id'          => $cart->id,
                            'affiliation' => Configuration::get('PS_SHOP_NAME'),
                            'revenue'     => $cart->getOrderTotal(true),
                            'shipping'    => $cart->getTotalShippingCost(),
                            'tax'         => $cart->getOrderTotal(true) - $cart->getOrderTotal(false),
                            'currency'    => $currencyInfo['iso_code'],
                        ],
                    ],
                ];

                foreach ($cart->getProducts() as $product) {
                    $gaConversion[] = [
                        'command'    => 'ecommerce:addItem',
                        'properties' => [
                            'id'       => $cart->id,
                            'name'     => $product['name'],
                            'sku'      => $product['reference'],
                            'category' => $product['category'],
                            'price'    => $product['price'],
                            'quantity' => $product['quantity'],
                        ],
                    ];
                }

                $this->context->smarty->assign('ga_conversion', $gaConversion);
            }
        } catch(Exception $e) {
            Logger::addLog(__METHOD__ . ' ' . $e->getMessage(), 1);
        }

        $this->setTemplate(LeaseNow::buildTemplatePath('success', 'front'));
    }
}
