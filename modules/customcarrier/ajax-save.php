<?php
/**
 * Custom Carrier - Direct AJAX Save Handler
 * Saves product transport settings from PS8 product page.
 */

// Load PrestaShop bootstrap
$currentDir = dirname(__FILE__);
$configFile = $currentDir . '/../../config/config.inc.php';

if (!file_exists($configFile)) {
    die(json_encode(['success' => false, 'message' => 'Config not found']));
}

require_once $configFile;

header('Content-Type: application/json');

// Verify admin employee via admin cookie
$cookie = new Cookie('psAdmin');

if (empty($cookie->id_employee)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'debug' => 'no_employee']);
    exit;
}

$employee = new Employee((int) $cookie->id_employee);
if (!Validate::isLoadedObject($employee)) {
    echo json_encode(['success' => false, 'message' => 'Invalid employee']);
    exit;
}

// Process save
$action = Tools::getValue('action');
if ($action !== 'saveProductSettings') {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

$idProduct = (int) Tools::getValue('customcarrier_id_product');
if ($idProduct <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

$module = Module::getInstanceByName('customcarrier');
if (!$module) {
    echo json_encode(['success' => false, 'message' => 'Module not found']);
    exit;
}

$data = [
    'free_shipping' => (int) Tools::getValue('customcarrier_free_shipping'),
    'base_shipping_cost' => (float) Tools::getValue('customcarrier_base_shipping_cost'),
    'multiply_by_quantity' => (int) Tools::getValue('customcarrier_multiply_by_quantity'),
    'free_shipping_quantity' => (int) Tools::getValue('customcarrier_free_shipping_quantity'),
    'free_shipping_from_price' => Tools::getValue('customcarrier_free_shipping_from_price') !== '' ? (float) Tools::getValue('customcarrier_free_shipping_from_price') : null,
    'apply_threshold' => (int) Tools::getValue('customcarrier_apply_threshold'),
    'separate_package' => (int) Tools::getValue('customcarrier_separate_package'),
    'exclude_from_free_shipping' => (int) Tools::getValue('customcarrier_exclude_from_free_shipping'),
    'max_quantity_per_package' => Tools::getValue('customcarrier_max_quantity_per_package') ? (int) Tools::getValue('customcarrier_max_quantity_per_package') : null,
    'max_weight_per_package' => Tools::getValue('customcarrier_max_weight_per_package') !== '' ? (float) Tools::getValue('customcarrier_max_weight_per_package') : null,
    'max_packages' => Tools::getValue('customcarrier_max_packages') ? (int) Tools::getValue('customcarrier_max_packages') : null,
    'cost_above_max_packages' => Tools::getValue('customcarrier_cost_above_max_packages') ? (float) Tools::getValue('customcarrier_cost_above_max_packages') : null,
];

$result = $module->saveProductTransportSettings($idProduct, $data);

echo json_encode([
    'success' => $result,
    'message' => $result ? 'OK' : 'Save failed',
]);
