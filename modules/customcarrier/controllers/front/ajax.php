<?php
/**
 * Custom Carrier - AJAX Controller
 * Handles AJAX requests for saving product transport settings from PS8 product page.
 */

class CustomcarrierAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        // Verify admin employee via cookie
        $cookie = new Cookie('psAdmin');
        if (empty($cookie->id_employee)) {
            $this->ajaxResponse(false, 'Unauthorized');
            return;
        }

        $employee = new Employee((int) $cookie->id_employee);
        if (!Validate::isLoadedObject($employee)) {
            $this->ajaxResponse(false, 'Unauthorized');
            return;
        }

        $action = Tools::getValue('action');

        if ($action === 'saveProductSettings') {
            $this->saveProductSettings();
        } else {
            $this->ajaxResponse(false, 'Unknown action');
        }
    }

    protected function saveProductSettings()
    {
        $idProduct = (int) Tools::getValue('customcarrier_id_product');

        if ($idProduct <= 0) {
            $this->ajaxResponse(false, 'Invalid product ID');
            return;
        }

        $data = [
            'free_shipping' => (int) Tools::getValue('customcarrier_free_shipping'),
            'base_shipping_cost' => (float) Tools::getValue('customcarrier_base_shipping_cost'),
            'multiply_by_quantity' => (int) Tools::getValue('customcarrier_multiply_by_quantity'),
            'free_shipping_quantity' => (int) Tools::getValue('customcarrier_free_shipping_quantity'),
            'apply_threshold' => (int) Tools::getValue('customcarrier_apply_threshold'),
            'separate_package' => (int) Tools::getValue('customcarrier_separate_package'),
            'exclude_from_free_shipping' => (int) Tools::getValue('customcarrier_exclude_from_free_shipping'),
            'max_quantity_per_package' => Tools::getValue('customcarrier_max_quantity_per_package') ? (int) Tools::getValue('customcarrier_max_quantity_per_package') : null,
            'max_packages' => Tools::getValue('customcarrier_max_packages') ? (int) Tools::getValue('customcarrier_max_packages') : null,
            'cost_above_max_packages' => Tools::getValue('customcarrier_cost_above_max_packages') ? (float) Tools::getValue('customcarrier_cost_above_max_packages') : null,
        ];

        $result = $this->module->saveProductTransportSettings($idProduct, $data);

        $this->ajaxResponse($result, $result ? 'OK' : 'Save failed');
    }

    protected function ajaxResponse(bool $success, string $message = '')
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        exit;
    }
}
