<?php
/**
 * Cart Accessories Module
 * Displays accessories from all products in the cart
 *
 * @author Norwit
 * @copyright 2026
 * @license MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class CartAccessories extends Module
{
    public function __construct()
    {
        $this->name = 'cartaccessories';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cart Accessories');
        $this->description = $this->l('Displays accessories from all products in the shopping cart.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayCartAccessories')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('CARTACCESSORIES_NBR', 8)
            && Configuration::updateValue('CARTACCESSORIES_TITLE', 'Przydatne akcesoria');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('CARTACCESSORIES_NBR')
            && Configuration::deleteByName('CARTACCESSORIES_TITLE');
    }

    /**
     * Module configuration page
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitCartAccessories')) {
            $nbr = (int) Tools::getValue('CARTACCESSORIES_NBR');
            $title = Tools::getValue('CARTACCESSORIES_TITLE');

            if ($nbr < 1 || $nbr > 20) {
                $output .= $this->displayError($this->l('Number of products must be between 1 and 20.'));
            } else {
                Configuration::updateValue('CARTACCESSORIES_NBR', $nbr);
                Configuration::updateValue('CARTACCESSORIES_TITLE', $title);
                $output .= $this->displayConfirmation($this->l('Settings updated successfully.'));
            }
        }

        return $output . $this->displayForm();
    }

    /**
     * Build configuration form
     */
    protected function displayForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Cart Accessories Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Section title'),
                        'name' => 'CARTACCESSORIES_TITLE',
                        'desc' => $this->l('Title displayed above accessories.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Number of products'),
                        'name' => 'CARTACCESSORIES_NBR',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Maximum number of accessories to display (1-20).'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitCartAccessories';

        $helper->fields_value['CARTACCESSORIES_NBR'] = Configuration::get('CARTACCESSORIES_NBR');
        $helper->fields_value['CARTACCESSORIES_TITLE'] = Configuration::get('CARTACCESSORIES_TITLE');

        return $helper->generateForm([$fields_form]);
    }

    /**
     * Hook to add CSS and JS
     */
    public function hookActionFrontControllerSetMedia()
    {
        if ($this->context->controller->php_self === 'cart') {
            $this->context->controller->registerStylesheet(
                'cartaccessories-css',
                'modules/' . $this->name . '/views/css/cartaccessories.css',
                ['media' => 'all', 'priority' => 200]
            );

            $this->context->controller->registerJavascript(
                'cartaccessories-js',
                'modules/' . $this->name . '/views/js/cartaccessories.js',
                ['position' => 'bottom', 'priority' => 200]
            );
        }
    }

    /**
     * Custom hook to display cart accessories
     */
    public function hookDisplayCartAccessories($params)
    {
        if (!isset($this->context->cart) || $this->context->cart->nbProducts() == 0) {
            return '';
        }

        $accessories = $this->getCartAccessories();

        if (empty($accessories)) {
            return '';
        }

        $this->context->smarty->assign([
            'accessories' => $accessories,
            'accessories_title' => Configuration::get('CARTACCESSORIES_TITLE'),
        ]);

        return $this->fetch('module:cartaccessories/views/templates/hook/accessories.tpl');
    }

    /**
     * Get accessories from all products in the cart
     */
    private function getCartAccessories()
    {
        $cartProducts = $this->context->cart->getProducts();

        if (empty($cartProducts)) {
            return [];
        }

        $maxProducts = (int) Configuration::get('CARTACCESSORIES_NBR');
        $cartProductIds = array_column($cartProducts, 'id_product');
        $allAccessoryIds = [];

        // Get accessories for each product in cart
        foreach ($cartProductIds as $productId) {
            $product = new Product($productId, false, $this->context->language->id);
            $productAccessories = $product->getAccessories($this->context->language->id);

            if (!empty($productAccessories)) {
                foreach ($productAccessories as $accessory) {
                    $accessoryId = (int) $accessory['id_product'];

                    // Don't include products already in cart
                    if (!in_array($accessoryId, $cartProductIds) && !in_array($accessoryId, $allAccessoryIds)) {
                        $allAccessoryIds[] = $accessoryId;
                    }
                }
            }
        }

        if (empty($allAccessoryIds)) {
            return [];
        }

        // Limit number of accessories
        $allAccessoryIds = array_slice($allAccessoryIds, 0, $maxProducts);

        // Prepare products for template using PrestaShop presenter
        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();

        if (version_compare(_PS_VERSION_, '1.7.5', '>=')) {
            $presenter = new \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter(
                new ImageRetriever($this->context->link),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->context->getTranslator()
            );
        } else {
            $presenter = new \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
                new ImageRetriever($this->context->link),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->context->getTranslator()
            );
        }

        $productsForTemplate = [];

        foreach ($allAccessoryIds as $accessoryId) {
            try {
                $rawProduct = ['id_product' => $accessoryId];

                if (method_exists($assembler, 'assembleProduct')) {
                    $assembledProduct = $assembler->assembleProduct($rawProduct);

                    if ($assembledProduct) {
                        $productsForTemplate[] = $presenter->present(
                            $presentationSettings,
                            $assembledProduct,
                            $this->context->language
                        );
                    }
                }
            } catch (Exception $e) {
                // Skip products that can't be assembled
                continue;
            }
        }

        return $productsForTemplate;
    }
}
