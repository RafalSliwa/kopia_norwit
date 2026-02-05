<?php
/**
 * Norwit GTM - Data Layer dla konwersji
 *
 * Dodaje dataLayer.push() na stronie potwierdzenia zamówienia
 * dla Google Tag Manager i Google Ads Conversion Tracking.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class NorwitGtm extends Module
{
    public function __construct()
    {
        $this->name = 'norwitgtm';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Norwit GTM Data Layer');
        $this->description = $this->l('Dodaje dataLayer dla GTM na stronie potwierdzenia zamówienia');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayOrderConfirmation');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Hook na stronie potwierdzenia zamówienia
     */
    public function hookDisplayOrderConfirmation($params)
    {
        if (!isset($params['order'])) {
            return '';
        }

        $order = $params['order'];

        // Pobierz produkty z zamówienia
        $products = $order->getProducts();
        $items = [];

        foreach ($products as $product) {
            $items[] = [
                'item_id' => $product['product_reference'] ?: $product['product_id'],
                'item_name' => $product['product_name'],
                'price' => (float) $product['unit_price_tax_incl'],
                'quantity' => (int) $product['product_quantity'],
            ];
        }

        // Przygotuj dane dla dataLayer
        $dataLayerData = [
            'event' => 'purchase',
            'ecommerce' => [
                'transaction_id' => $order->reference,
                'value' => (float) $order->total_paid_tax_incl,
                'currency' => 'PLN',
                'items' => $items,
            ],
        ];

        $jsonData = json_encode($dataLayerData, JSON_UNESCAPED_UNICODE);

        return "
        <script>
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({$jsonData});
            console.log('[NorwitGTM] Purchase dataLayer push:', {$jsonData});
        </script>
        ";
    }
}
