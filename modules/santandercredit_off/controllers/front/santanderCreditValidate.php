<?php

class santandercreditsantanderCreditValidateModuleFrontController extends ModuleFrontController
{

    public function postProcess()
    {
        if (!($this->module instanceof SantanderCredit)) {
            Tools::redirect('index.php?controller=order&step=1');

            return;
        }
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');

        $currency = $this->context->currency;
        $total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
        $useOrderState = Configuration::get('SANTANDERCREDIT_USE_ORDER_STATE');


        $this->module->validateOrder(
            $cart->id,
            (int) Configuration::get($useOrderState),
            $total,
            $this->module->displayName,
            NULL,
            [],
            $currency->id,
            false,
            $customer->secure_key
        );


        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
    }

}
