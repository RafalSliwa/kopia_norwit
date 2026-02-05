<?php
/**
 * Free Shipping Progress AJAX Controller
 * Returns shipping data for cart updates
 */

class FreeShippingProgressAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;
        parent::initContent();
    }

    public function displayAjax()
    {
        header('Content-Type: application/json');

        // Check if cart exists
        if (!$this->context->cart || $this->context->cart->nbProducts() == 0) {
            die(json_encode([
                'success' => false,
                'message' => 'Empty cart'
            ]));
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

        // Always calculate shipping cost from database
        $baseShippingCost = $this->getLowestShippingPrice();

        // Get shipping cost to display
        $shippingCost = $isFreeShipping ? 0 : $baseShippingCost;

        die(json_encode([
            'success' => true,
            'cart_total' => $cartTotal,
            'shipping_cost' => $shippingCost,
            'base_shipping_cost' => $baseShippingCost,
            'free_shipping_threshold' => $freeShippingThreshold,
            'amount_needed' => $amountNeeded,
            'progress_percentage' => $progressPercentage,
            'is_free_shipping' => $isFreeShipping,
            'has_additional_shipping_cost' => $hasAdditionalShippingCost
        ]));
    }

    /**
     * Get free shipping threshold from carriers
     */
    private function getFreeShippingThreshold()
    {
        $db = Db::getInstance();

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

        return $this->getLowestShippingPriceFromDb();
    }

    /**
     * Get lowest shipping price from database based on cart total
     */
    private function getLowestShippingPriceFromDb()
    {
        $db = Db::getInstance();
        $cartTotal = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

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
            return (float) $result * 1.23;
        }

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

        return 14.99;
    }
}
