<?php
/**
 * Clear Cart AJAX Controller
 */

class ClearCartAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Verify token
        if (!Tools::getValue('token') || Tools::getValue('token') !== Tools::getToken(false)) {
            $this->ajaxResponse(['success' => false, 'error' => 'Invalid token']);
            return;
        }

        // Check if action is clear
        if (Tools::getValue('action') !== 'clear') {
            $this->ajaxResponse(['success' => false, 'error' => 'Invalid action']);
            return;
        }

        // Get cart
        $cart = $this->context->cart;

        if (!Validate::isLoadedObject($cart)) {
            $this->ajaxResponse(['success' => false, 'error' => 'Cart not found']);
            return;
        }

        // Get all products from cart
        $products = $cart->getProducts();

        if (empty($products)) {
            $this->ajaxResponse(['success' => true, 'message' => 'Cart is already empty']);
            return;
        }

        // Remove all products
        $success = true;
        foreach ($products as $product) {
            $result = $cart->deleteProduct(
                (int) $product['id_product'],
                (int) $product['id_product_attribute'],
                (int) $product['id_customization'],
                (int) $product['id_address_delivery']
            );

            if (!$result) {
                $success = false;
            }
        }

        if ($success) {
            // Trigger cart update event
            Hook::exec('actionCartSave', ['cart' => $cart]);

            $this->ajaxResponse([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart_url' => $this->context->link->getPageLink('cart')
            ]);
        } else {
            $this->ajaxResponse(['success' => false, 'error' => 'Failed to clear cart']);
        }
    }

    /**
     * Send JSON response
     */
    private function ajaxResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
