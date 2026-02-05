<?php

require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../init.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

$action = Tools::getValue('action');

if ($action === 'getShippingInfo') {
    $id_product = (int)Tools::getValue('id_product');
    
    if (!$id_product) {
        echo json_encode(['error' => 'No product ID provided']);
        exit;
    }
    
    require_once dirname(__FILE__) . '/relatedproducts.php';
    $module = new RelatedProducts();
    $module->getShippingInfo($id_product);

} elseif ($action === 'getFreeShippingThreshold') {
    $id_product = (int)Tools::getValue('id_product');
    
    require_once dirname(__FILE__) . '/relatedproducts.php';
    $module = new RelatedProducts();
    $module->getFreeShippingThresholdInfo($id_product);

} elseif ($action === 'getShippingCost') {
    $id_product = (int)Tools::getValue('id_product');
    
    require_once dirname(__FILE__) . '/relatedproducts.php';
    $module = new RelatedProducts();
    $module->getShippingCostInfo($id_product);
    
} elseif ($action === 'getFreeShippingInfo') {
    header('Content-Type: application/json');
    
    $context = Context::getContext();
    
    if (!$context->cart) {
        echo json_encode(['error' => 'No cart found']);
        exit;
    }

    $cart = $context->cart;
    $cartTotal = (float) $cart->getOrderTotal(true, Cart::BOTH);
    $cartWeight = (float) $cart->getTotalWeight();

    $freeShippingPrice = (float) Configuration::get('PS_SHIPPING_FREE_PRICE');
    $freeShippingWeight = (float) Configuration::get('PS_SHIPPING_FREE_WEIGHT');

    $mode = 'none';
    $threshold = 0.0;
    $remaining = 0.0;
    $hasFreeShipping = false;
    $progressPercentage = 0.0;
    $formattedThreshold = '';
    $formattedRemaining = '';
    $isOverWeightLimit = false;
    $isFallbackThreshold = false;

    if ($freeShippingPrice > 0) {
        $mode = 'price';
        $threshold = $freeShippingPrice;
        $remaining = $threshold - $cartTotal;
        $hasFreeShipping = $remaining <= 0;
        $progressPercentage = $threshold > 0 ? min(100, ($cartTotal / $threshold) * 100) : 100;
        $formattedThreshold = Tools::displayPrice($threshold);
        $formattedRemaining = Tools::displayPrice($hasFreeShipping ? 0 : max(0, $remaining));
    } elseif ($freeShippingWeight > 0) {
        $mode = 'weight';
        $threshold = $freeShippingWeight;
        $remaining = $threshold - $cartWeight;
        $hasFreeShipping = $threshold > 0 && $cartWeight <= $threshold;
        $progressPercentage = $threshold > 0 ? min(100, ($cartWeight / $threshold) * 100) : 0;
        $weightUnit = Configuration::get('PS_WEIGHT_UNIT');
        $formattedThreshold = sprintf('%0.2f %s', Tools::ps_round($threshold, 2), $weightUnit);
        $formattedRemaining = sprintf('%0.2f %s', Tools::ps_round($hasFreeShipping ? 0 : max(0, $remaining), 2), $weightUnit);
        $isOverWeightLimit = !$hasFreeShipping && $threshold > 0 && $cartWeight > $threshold;
    } else {
        // No configuration - use minimum threshold
        $mode = 'price';
        $threshold = 0.0;
        $remaining = 0.0;
        $hasFreeShipping = true;
        $progressPercentage = 100.0;
        $formattedThreshold = Tools::displayPrice(0);
        $formattedRemaining = Tools::displayPrice(0);
        $isFallbackThreshold = true;
    }

    $result = [
        'mode' => $mode,
        'cartTotal' => $cartTotal,
        'cartWeight' => $cartWeight,
        'threshold' => $threshold,
        'remaining' => $remaining,
        'progressPercentage' => round($progressPercentage, 2),
        'hasFreeShipping' => $hasFreeShipping,
        'isOverWeightLimit' => $isOverWeightLimit,
        'isFallbackThreshold' => $isFallbackThreshold,
        'formattedCartTotal' => Tools::displayPrice($cartTotal),
        'formattedThreshold' => $formattedThreshold,
        'formattedRemaining' => $formattedRemaining,
        'formattedFreeShippingAmount' => $formattedThreshold,
        'formattedRemainingAmount' => $formattedRemaining,
    ];
    
    echo json_encode($result);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
exit;