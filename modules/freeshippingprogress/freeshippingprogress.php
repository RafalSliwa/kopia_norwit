<?php
/**
 * Free Shipping Progress Module
 * Displays delivery cost and progress bar to free shipping in cart summary
 *
 * @author Norwit
 * @copyright 2026
 * @license MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class FreeShippingProgress extends Module
{
    public function __construct()
    {
        $this->name = 'freeshippingprogress';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Free Shipping Progress');
        $this->description = $this->l('Displays delivery cost and progress bar to free shipping in cart summary.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayCartDeliveryProgress')
            && $this->registerHook('actionFrontControllerSetMedia');
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
        // Only on cart page
        if ($this->context->controller->php_self !== 'cart') {
            return;
        }

        $this->context->controller->registerStylesheet(
            'freeshippingprogress-css',
            'modules/' . $this->name . '/views/css/freeshippingprogress.css',
            ['media' => 'all', 'priority' => 200]
        );

        $this->context->controller->registerJavascript(
            'freeshippingprogress-js',
            'modules/' . $this->name . '/views/js/freeshippingprogress.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }

    /**
     * Custom hook to display delivery progress in cart summary
     */
    public function hookDisplayCartDeliveryProgress($params)
    {
        // Check if cart exists and has products
        if (!isset($this->context->cart) || $this->context->cart->nbProducts() == 0) {
            return '';
        }

        // Get cart data
        $cartTotal = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

        // Get free shipping threshold
        $freeShippingThreshold = $this->getFreeShippingThreshold();

        // Check if any product has additional_shipping_cost
        $hasAdditionalShippingCost = $this->cartHasAdditionalShippingCost();

        // Calculate progress
        $amountNeeded = max(0, $freeShippingThreshold - $cartTotal);
        $progressPercentage = 0;
        if ($freeShippingThreshold > 0) {
            $progressPercentage = min(100, ($cartTotal / $freeShippingThreshold) * 100);
        }

        $isFreeShipping = ($freeShippingThreshold > 0) && ($cartTotal >= $freeShippingThreshold) && !$hasAdditionalShippingCost;

        // Always calculate shipping cost from database (even if free shipping applies)
        // This is needed for JavaScript to have the original price when cart changes
        $baseShippingCost = $this->getLowestShippingPrice();

        // Get shipping cost to display
        if ($isFreeShipping) {
            $shippingCost = 0;
        } else {
            $shippingCost = $baseShippingCost;
        }

        // Assign variables to Smarty
        $this->context->smarty->assign([
            'cart_total' => $cartTotal,
            'shipping_cost' => $shippingCost,
            'shipping_cost_formatted' => Tools::displayPrice($shippingCost),
            'base_shipping_cost' => $baseShippingCost,
            'free_shipping_threshold' => $freeShippingThreshold,
            'amount_needed' => $amountNeeded,
            'amount_needed_formatted' => Tools::displayPrice($amountNeeded),
            'progress_percentage' => $progressPercentage,
            'is_free_shipping' => $isFreeShipping,
            'has_additional_shipping_cost' => $hasAdditionalShippingCost,
            'currency_sign' => $this->context->currency->sign,
        ]);

        return $this->fetch('module:freeshippingprogress/views/templates/hook/progress.tpl');
    }

    /**
     * Get free shipping threshold from carriers
     */
    private function getFreeShippingThreshold()
    {
        $db = Db::getInstance();

        // Query for lowest threshold where delivery = 0 (free)
        $query = '
            SELECT MIN(COALESCE(rw.delimiter1, rp.delimiter1)) AS free_threshold
            FROM ' . _DB_PREFIX_ . 'delivery d
            INNER JOIN ' . _DB_PREFIX_ . 'carrier c ON d.id_carrier = c.id_carrier
            LEFT JOIN ' . _DB_PREFIX_ . 'range_weight rw ON rw.id_range_weight = d.id_range_weight
            LEFT JOIN ' . _DB_PREFIX_ . 'range_price rp ON rp.id_range_price = d.id_range_price
            WHERE c.active = 1
              AND c.deleted = 0
              AND d.id_zone = 1
              AND d.price = 0
              AND (rw.delimiter1 > 0 OR rp.delimiter1 > 0)
        ';

        $threshold = $db->getValue($query);

        // Fallback to PrestaShop configuration
        if (!$threshold || $threshold <= 0) {
            $threshold = Configuration::get('PS_SHIPPING_FREE_PRICE');
        }

        return (float) $threshold ?: 0;
    }

    /**
     * Check if any product in cart has additional_shipping_cost
     */
    private function cartHasAdditionalShippingCost()
    {
        $products = $this->context->cart->getProducts();

        foreach ($products as $product) {
            if (isset($product['additional_shipping_cost']) && (float) $product['additional_shipping_cost'] > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get lowest shipping price for current cart
     */
    private function getLowestShippingPrice()
    {
        if (!$this->context->cart || !$this->context->cart->id) {
            return 0;
        }

        // First try to use simulateCarriersOutput if user has address
        try {
            $carriers = $this->context->cart->simulateCarriersOutput(null, true);
            $lowestPrice = null;

            foreach ($carriers as $carrier) {
                $carrierPrice = 0;

                if (isset($carrier['price_with_tax'])) {
                    $carrierPrice = (float) $carrier['price_with_tax'];
                } elseif (isset($carrier['total_price_with_tax'])) {
                    $carrierPrice = (float) $carrier['total_price_with_tax'];
                } elseif (isset($carrier['price'])) {
                    $carrierPrice = (float) $carrier['price'];
                }

                // Skip pickup options
                $isPickup = (
                    stripos($carrier['name'], 'sklep') !== false ||
                    stripos($carrier['name'], 'pickup') !== false ||
                    stripos($carrier['name'], 'odbiór') !== false ||
                    stripos($carrier['name'], 'magazyn') !== false
                );

                if ($isPickup) {
                    continue;
                }

                if ($carrierPrice > 0 && ($lowestPrice === null || $carrierPrice < $lowestPrice)) {
                    $lowestPrice = $carrierPrice;
                }
            }

            if ($lowestPrice !== null && $lowestPrice > 0) {
                return $lowestPrice;
            }
        } catch (Exception $e) {
            // Fall through to database query
        }

        // Fallback: get lowest price from database for cart total
        return $this->getLowestShippingPriceFromDb();
    }

    /**
     * Get lowest shipping price from database based on cart total
     */
    private function getLowestShippingPriceFromDb()
    {
        $db = Db::getInstance();
        $cartTotal = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

        // Get lowest delivery price for current cart total (price range)
        $query = '
            SELECT MIN(d.price) as min_price
            FROM ' . _DB_PREFIX_ . 'delivery d
            INNER JOIN ' . _DB_PREFIX_ . 'carrier c ON d.id_carrier = c.id_carrier
            LEFT JOIN ' . _DB_PREFIX_ . 'range_price rp ON rp.id_range_price = d.id_range_price
            WHERE c.active = 1
              AND c.deleted = 0
              AND d.id_zone = 1
              AND d.price > 0
              AND rp.delimiter1 <= ' . (float) $cartTotal . '
              AND rp.delimiter2 > ' . (float) $cartTotal . '
        ';

        $result = $db->getValue($query);

        if ($result && $result > 0) {
            // Add 23% VAT
            return (float) $result * 1.23;
        }

        // Fallback: get any lowest price (any zone)
        $fallbackQuery = '
            SELECT MIN(d.price) as min_price
            FROM ' . _DB_PREFIX_ . 'delivery d
            INNER JOIN ' . _DB_PREFIX_ . 'carrier c ON d.id_carrier = c.id_carrier
            WHERE c.active = 1
              AND c.deleted = 0
              AND d.price > 0
        ';

        $fallbackResult = $db->getValue($fallbackQuery);

        if ($fallbackResult && $fallbackResult > 0) {
            return (float) $fallbackResult * 1.23;
        }

        // Last fallback: fixed default price
        return 14.99;
    }
}
