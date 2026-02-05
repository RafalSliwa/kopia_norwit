<?php
/**
 * Clear Cart Module
 *
 * @author Norwit
 * @copyright 2026
 * @license MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ClearCart extends Module
{
    public function __construct()
    {
        $this->name = 'clearcart';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Clear Cart');
        $this->description = $this->l('Adds clear cart button, product counter in cart header, and sorts products by stock availability (in-stock first).');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayCartHeader')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('actionPresentCart');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Hook to add CSS and JS
     */
    public function hookActionFrontControllerSetMedia()
    {
        if ($this->context->controller->php_self === 'cart') {
            $this->context->controller->registerStylesheet(
                'clearcart-css',
                'modules/' . $this->name . '/views/css/clearcart.css',
                ['media' => 'all', 'priority' => 200]
            );

            $this->context->controller->registerJavascript(
                'clearcart-js',
                'modules/' . $this->name . '/views/js/clearcart.js',
                ['position' => 'bottom', 'priority' => 200]
            );

            Media::addJsDef([
                'clearcart_ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax'),
                'clearcart_token' => Tools::getToken(false),
                'clearcart_confirm_message' => $this->l('Are you sure you want to remove all products from cart?')
            ]);
        }
    }

    /**
     * Hook to display product counter and clear cart button in cart header
     */
    public function hookDisplayCartHeader($params)
    {
        if (!isset($this->context->cart) || $this->context->cart->nbProducts() == 0) {
            return '';
        }

        // Build summary string for product counter
        $nbProducts = $this->context->cart->nbProducts();
        $productWord = $this->getProductWord($nbProducts);

        $this->context->smarty->assign([
            'clearcart_summary_string' => $nbProducts . ' ' . $productWord,
            'clearcart_product_count' => $nbProducts
        ]);

        return $this->fetch('module:clearcart/views/templates/hook/button.tpl');
    }

    /**
     * Get translated product word based on count (Polish plural forms)
     *
     * @param int $count
     * @return string
     */
    private function getProductWord($count)
    {
        // Polish plural rules:
        // 1 -> produkt
        // 2-4, 22-24, 32-34... -> produkty
        // 0, 5-21, 25-31... -> produktów
        $absCount = abs($count);
        $lastDigit = $absCount % 10;
        $lastTwoDigits = $absCount % 100;

        if ($absCount == 1) {
            return $this->l('product');
        } elseif ($lastDigit >= 2 && $lastDigit <= 4 && ($lastTwoDigits < 10 || $lastTwoDigits >= 20)) {
            return $this->l('products');
        } else {
            return $this->l('products many');
        }
    }

    /**
     * Hook to sort cart products - in-stock first, out-of-stock last
     */
    public function hookActionPresentCart($params)
    {
        if (!isset($params['presentedCart'])) {
            return;
        }

        $cartLazyArray = $params['presentedCart'];

        // Get products array
        $products = $cartLazyArray['products'];

        if (empty($products) || !is_array($products)) {
            return;
        }

        // Sort: in-stock products first, out-of-stock last
        usort($products, function ($a, $b) {
            $aInStock = isset($a['quantity_available']) ? (int) $a['quantity_available'] > 0 : true;
            $bInStock = isset($b['quantity_available']) ? (int) $b['quantity_available'] > 0 : true;

            // If both have same stock status, keep original order
            if ($aInStock === $bInStock) {
                return 0;
            }

            // In-stock products first
            return $aInStock ? -1 : 1;
        });

        // Update the cart products
        $cartLazyArray['products'] = $products;
    }
}
