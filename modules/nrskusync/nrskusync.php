<?php
/**
 * NR SKU Sync – PrestaShop 8
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Nrskusync extends Module
{
    public function __construct()
    {
        $this->name = 'nrskusync';
        $this->version = '1.1.0';
        $this->author = 'Norwit';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;

        parent::__construct();

        // Użycie l() zamiast trans() – dla tłumaczeń w panelu
        $this->displayName = $this->l('NR SKU Sync');
        $this->description = $this->l('Updates product reference (SKU) when combination changes and adds a "product with variants" flag.');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionFrontControllerSetMedia')  
            && $this->registerHook('actionProductFlagsModifier')      
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayProductCurrentVariant');                  
    }

    public function hookDisplayProductCurrentVariant($params)
    {
        if (empty($params['product']) || empty($params['product']['attributes'])) {
            return '';
        }

        $this->context->smarty->assign([
            'product_attributes' => $params['product']['attributes'],
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/current_variant.tpl');
    }

    /**
     * Load our JS only on the product page.
     */
    public function hookActionFrontControllerSetMedia(array $params)
    {
        $controller = $this->context->controller;
        if (!$controller) {
            return;
        }

        if ($controller->php_self === 'product') {
            $controller->registerJavascript(
                'module-' . $this->name . '-sku-sync',
                'modules/' . $this->name . '/views/js/sku-sync.js',
                [
                    'position' => 'bottom',
                    'priority' => 50,
                    'server' => 'current',
                    'attributes' => ['data-nrskusync' => '1'],
                ]
            );
        }
    }

    /**
     * Subtle CSS for the flag (optional).
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            $this->name . '-flags',
            'modules/' . $this->name . '/views/css/nrskusync.css',
            ['media' => 'all', 'priority' => 50]
        );
    }

    /**
     * Modify product flags (card and listing) – add "Product with variants".
     */
    public function hookActionProductFlagsModifier(array $params)
    {
        $product = $params['product'] ?? null;
        if (!$product || !$this->hasVariants($product)) {
            return $params;
        }

        // Do not duplicate if already exists
        foreach ($params['flags'] as $flag) {
            if (!empty($flag['type']) && $flag['type'] === 'variants') {
                return $params;
            }
        }

        // Label translated via l()
        $label = $this->l('Product with variants');

        $params['flags'][] = [
            'type'      => 'variants',
            'label'     => $label,
            'position'  => 30,
            'css_class' => 'product-flag-variants',
        ];

        return $params;
    }

    /**
     * Check if product has variants – compatible with PS8.
     */
    private function hasVariants($product): bool
    {
        if (is_array($product)) {
            if (!empty($product['main_variants'])) return true;
            if (!empty($product['combinations'])) return true;
            if (!empty($product['cache_default_attribute'])) return true;
            if (!empty($product['attributes']) && is_array($product['attributes'])) return true;
            if (!empty($product['id_product_attribute'])) return true;
            return false;
        }

        if ($product instanceof \Product) {
            return method_exists($product, 'hasAttributes') ? (bool) $product->hasAttributes() : false;
        }

        return false;
    }
}
