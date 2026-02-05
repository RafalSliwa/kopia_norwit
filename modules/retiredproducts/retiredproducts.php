<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetiredProducts extends Module
{
    public function __construct()
    {
        $this->name = 'retiredproducts';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Norwit';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Withdrawal Manager');
        $this->description = $this->l('Adds a flag to mark products as retired and display a custom template.');
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        ];
    }

    public function install()
    {
        return parent::install()
            && $this->addRetiredColumn()
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionProductFlagsModifier')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionPresentProduct')
            && $this->registerHook('displayRetiredProduct'); // new custom hook
    }

    public function uninstall()
    {
        return $this->removeRetiredColumn() && parent::uninstall();
    }

    private function addRetiredColumn()
    {
        return Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'product`
             ADD `retired` TINYINT(1) NOT NULL DEFAULT 0,
             ADD `id_product_redirect` INT(10) UNSIGNED DEFAULT NULL'
        );
    }

    private function removeRetiredColumn()
    {
        return Db::getInstance()->execute(
            'ALTER TABLE `'._DB_PREFIX_.'product`
             DROP COLUMN `retired`,
             DROP COLUMN `id_product_redirect`'
        );
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') === 'AdminProducts') {
            $this->context->controller->addJS($this->_path . 'views/js/admin_autocomplete.js');
            $this->context->controller->addCSS($this->_path . 'views/css/admin_autocomplete.css');

            Media::addJsDef([
                // Użyj formatu legacy, aby uniknąć problemów z 404 w różnych środowiskach
                'retiredproducts_ajax_url' => $this->context->link->getModuleLink('retiredproducts', 'ajaxsearch', [], null, null, null, true),
            ]);
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = (int)$params['id_product'];

        $row = Db::getInstance()->getRow(
            'SELECT `retired`, `id_product_redirect`
             FROM `'._DB_PREFIX_.'product`
             WHERE id_product = ' . $id_product
        );

        $id_product_redirect_reference = '';
        if ($row['id_product_redirect']) {
            $id_product_redirect_reference = Db::getInstance()->getValue(
                'SELECT reference FROM '._DB_PREFIX_.'product WHERE id_product = '.(int)$row['id_product_redirect']
            );
        }
        $this->context->smarty->assign([
            'retired' => (bool)$row['retired'],
            'id_product_redirect' => (int)$row['id_product_redirect'],
            'id_product_redirect_name' => $row['id_product_redirect']
                ? Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'product_lang WHERE id_product = '.(int)$row['id_product_redirect'].' AND id_lang = '.(int)$this->context->language->id)
                : '',
            'id_product_redirect_reference' => $id_product_redirect_reference,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/product_tab.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $id_product = (int)$params['id_product'];

        // Pobierz poprzednie wartości z bazy
        $row = Db::getInstance()->getRow(
            'SELECT retired, id_product_redirect FROM '._DB_PREFIX_.'product WHERE id_product = '.$id_product
        );
        $retired_before = (int)$row['retired'];
        $redirect_before = $row['id_product_redirect'];

        $retired = (int)Tools::getValue('retired_product');
        $id_product_redirect = (int)Tools::getValue('id_product_redirect');

        // Jeśli produkt był wycofany i miał przekierowanie, a po update pole przekierowania jest puste, przywróć poprzednią wartość
        if ($retired_before == 1 && $redirect_before && $retired == 1 && !$id_product_redirect) {
            $id_product_redirect = (int)$redirect_before;
        }

        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'product`
             SET `retired` = ' . $retired . ',
                 `id_product_redirect` = ' . ($id_product_redirect ? $id_product_redirect : 'NULL') . '
             WHERE id_product = ' . $id_product
        );
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/retired.css', 'all');
        $this->context->smarty->assign('retiredproducts_img_url', $this->_path . 'views/img/');
    }

    public function hookActionProductFlagsModifier($params)
    {
        $id_product = (int)($params['product']['id_product'] ?? 0);
        if (!$id_product) {
            return $params;
        }

        $retired = (bool)Db::getInstance()->getValue(
            'SELECT `retired` FROM `'._DB_PREFIX_.'product` WHERE id_product = ' . $id_product
        );

        if ($retired) {
            $params['flags']['retired'] = [
                'type' => 'retired',
                'label' => $this->l('Withdrawn'),
                'position' => 5,
                'css_class' => 'product-flag-retired',
            ];
        }

        return $params;
    }

    public function hookActionPresentProduct($params)
    {
        $id_product = (int)$params['presentedProduct']['id_product'];
        $id_lang = (int)$this->context->language->id;
        $product_from = new Product($id_product, true, $id_lang);

        // ALWAYS fetch id_product_redirect from the database
        $id_product_redirect = (int)Db::getInstance()->getValue(
            'SELECT id_product_redirect FROM '._DB_PREFIX_.'product WHERE id_product = '.(int)$id_product
        );

        $product_to = null;
        if ($id_product_redirect) {
            $product_to = new Product($id_product_redirect, true, $id_lang);
        }

        // Images
        $product_from_image_id = null;
        $product_to_image_id = null;
        if ($product_from->id) {
            $cover = Image::getCover($product_from->id);
            $product_from_image_id = $cover['id_image'] ?? null;
        }
        if ($product_to && $product_to->id) {
            $cover = Image::getCover($product_to->id);
            $product_to_image_id = $cover['id_image'] ?? null;
        }

        // Replacement product prices
        $price_net = '';
        $price_gross = '';
        if ($product_to) {
            $price_net_raw = Product::getPriceStatic($product_to->id, false, null, 6);
            $price_gross_raw = Product::getPriceStatic($product_to->id, true, null, 6);
            $price_net = Tools::displayPrice($price_net_raw, $this->context->currency);
            $price_gross = Tools::displayPrice($price_gross_raw, $this->context->currency);
        }

        // Pass to presented product
        $params['presentedProduct']['product_from'] = $product_from;
        $params['presentedProduct']['product_to'] = $product_to;
        $params['presentedProduct']['product_from_image_id'] = $product_from_image_id;
        $params['presentedProduct']['product_to_image_id'] = $product_to_image_id;
        $params['presentedProduct']['redirect_product_price_net'] = $price_net;
        $params['presentedProduct']['redirect_product_price_gross'] = $price_gross;
        $params['presentedProduct']['retiredproducts_img_url'] = $this->_path . 'views/img/';
        $params['presentedProduct']['link'] = $this->context->link;

        $params['presentedProduct']['retired'] = (bool)Db::getInstance()->getValue(
            'SELECT retired FROM '._DB_PREFIX_.'product WHERE id_product = '.(int)$id_product
        );
        // No return needed, as the array is passed by reference
    }

    /**
     * Custom hook to render retired product block via {hook h='displayRetiredProduct' product=$product}
     */
    public function hookDisplayRetiredProduct($params)
    {
        if (empty($params['product'])) {
            return '';
        }
        return $this->getRetiredProductHtml($params['product']);
    }

    /**
     * Helper to render the retired product template
     */
    private function getRetiredProductHtml($product)
    {
        $this->context->smarty->assign('product', $product);
        return $this->context->smarty->fetch('module:retiredproducts/views/templates/front/product-retired.tpl');
    }
}
