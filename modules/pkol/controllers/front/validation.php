<?php
/**
 * PKO Leasing Integration Module
 *
 * Integruje płatności leasingowe PKO z PrestaShop.
 *
 * @author    PKO Leasing
 * @copyright 2024 PKO Leasing
 * @license   MIT License
 * @see       https://www.pkoleasing.pl/
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PkolValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        $this->langID = (int) Configuration::get('PS_LANG_DEFAULT');

        if ($cart->id_customer === 0 || $cart->id_address_delivery === 0 || $cart->id_address_invoice === 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] === 'pkol') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            exit($this->module->l('This payment method is not available.', 'validation'));
        }

        $pko = new Pkol();
        $products = $cart->getProducts();
        $productData = [];
        $total = 0;
        $total_wt = 0;

        foreach ($products as $n => $product) {
            $pid = $product['id_product'];
            $quantity = $product['cart_quantity'];
            $p = new Product($pid, false, $this->langID);

            if ($product['id_product_attribute'] > 0) {
                $product_data = $pko->getProduct($pid, $product['id_product_attribute']);
                $p->name = $product_data['product_name'];
                $price_without_tax = $product_data['price_without_tax'];
                $price_with_tax = $product_data['price_with_tax'];
            } else {
                $price_without_tax = number_format($p->getPriceStatic($pid, false), 2, '.', '');
                $price_with_tax = number_format($p->getPriceStatic($pid, true), 2, '.', '');
            }

            $productData[] = [
                'name' => $p->name,
                'price_with_tax' => $price_with_tax,
                'price_without_tax' => $price_without_tax,
                'quantity' => $quantity,
                'category' => $p->id_category_default,
                'vat_rate' => $pko->getRate($p->getTaxesRate()),
            ];

            $total += $price_without_tax * $quantity;
            $total_wt += $price_with_tax * $quantity;
        }

        $this->context->smarty->assign([
            'lease_url' => $pko->lease_url,
            'shopID' => $pko->shopID,
            'cart_id' => $cart->id,
            'return_link' => Tools::getHttpHost(true) . __PS_BASE_URI__,
            'unique_item_quantity' => count($products),
            'products' => $productData,
            'total_with_tax' => number_format($total_wt, 2, '.', ''),
            'total_without_tax' => number_format($total, 2, '.', ''),
            'type' => 'ORDER',
        ]);

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $mailVars = [];

        $this->module->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_BANKWIRE'),
            $total,
            $this->module->displayName,
            null,
            $mailVars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );
        $this->setTemplate('module:pkol/views/templates/front/leasing_form.tpl');
    }
}
