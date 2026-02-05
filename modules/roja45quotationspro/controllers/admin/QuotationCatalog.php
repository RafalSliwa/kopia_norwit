<?php
/**
 * QuotationStatusesController.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationStatusesController
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationStatusesController.
 * 2023 TOOLE - Inter-soft.com
 * All rights reserved.
 *
 * DISCLAIMER
 *
 * Changing this file will render any support provided by us null and void.
 *
 * @author    Toole <support@toole.com>
 * @copyright 2023 TOOLE - Inter-soft.com
 * @license   license.txt
 * @category  TooleAmazonMarketTool
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuotationCatalogController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();

        $this->override_folder = 'quotation_catalog/';
        $this->tpl_folder = 'quotation_catalog/';
        $this->bootstrap = true;
        $this->table = 'product';
        $this->identifier = 'id_product';
        $this->submit_action = 'submitAdd' . $this->table;
        $this->show_cancel_button = true;
        $this->className = 'Product';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;

        $this->explicitSelect = true;
        $this->addRowAction('updateEnabled');
        $this->addRowAction('view');
        $this->addRowAction('preview');
        if (!Tools::getValue('id_product')) {
            $this->multishop_context_group = false;
        }
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os',
        );
        $this->_defaultOrderBy = $this->identifier = 'id_product';
        $this->deleted = false;
        $this->_orderBy = null;
        $this->list_no_link = true;

        if (version_compare(_PS_VERSION_, '1.6.1', '<') == true) {
            $categories = RojaFortyFiveQuotationsProCore::getAllCategoriesName(
                2,
                $this->context->language->id,
                false
            );
        } else {
            $categories = Category::getAllCategoriesName(
                2,
                $this->context->language->id,
                false
            );
        }
        $category_array = array();
        foreach ($categories as $row) {
            $category_array[$row['id_category']] = $row['name'];
        }

        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ?
        (int) $this->context->shop->id : 'a.id_shop_default';
        $this->_select .=
            'shop.`name` AS `shop_name`,a.`id_shop_default`,a.`id_category_default`,cl.name as category_name,';

        $this->_join =
        ' JOIN `' . _DB_PREFIX_ . 'product_shop` sa ON
            (a.`id_product` = sa.`id_product` AND sa.id_shop = ' . $id_shop . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_quotationspro` pq ON
            (pq.id_product = a.`id_product` AND pq.id_shop = ' . $id_shop . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON
            (cl.id_lang = ' . (int) $this->context->language->id . ' AND
            cl.id_category=a.id_category_default AND cl.id_shop=' . $id_shop . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'shop` shop ON
            (shop.id_shop = ' . $id_shop . ')';

        $this->bulk_actions = array(
            'enable' => array(
                'text' => $this->l('Enable selected', 'QuotationCatalog'),
                'confirm' => $this->l('Enable selected items?', 'QuotationCatalog'),
                'icon' => 'icon-check',
            ),
            'disable' => array(
                'text' => $this->l('Disable selected', 'QuotationCatalog'),
                'confirm' => $this->l('Disable selected items?', 'QuotationCatalog'),
                'icon' => 'icon-times',
            ),
            'enableAll' => array(
                'text' => $this->l('ENABLE ALL', 'QuotationCatalog'),
                'confirm' => $this->l('Enable all products?', 'QuotationCatalog'),
                'icon' => 'icon-check',
            ),
        );

        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->l('ID'),
            'align' => 'text-center',
            'class' => 'fixed-width-sm',
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Name', 'QuotationCatalog'),
            'width' => 'auto',
            'filter_key' => 'b!name',
        );
        $this->fields_list['category_name'] = array(
            'title' => $this->l('Default Category', 'QuotationCatalog'),
            'width' => 'auto',
            'color' => 'color',
            'type' => 'select',
            'list' => $category_array,
            'filter_key' => 'a!id_category_default',
            'filter_type' => 'int',
            'order_key' => 'id',
        );

        $this->fields_list['reference'] = array(
            'title' => $this->l('Reference', 'QuotationCatalog'),
            'width' => 'auto',
            'filter_key' => 'a!reference',
        );

        if ((int) Configuration::get('ROJA45_QUOTATIONSPRO_ENABLEDEPOSITPAYMENTS')) {
            $this->fields_list['deposit_amount'] = array(
                'title' => $this->l('Deposit', 'QuotationCatalog'),
                'width' => 'auto',
                'filter_key' => 'pq!deposit_amount',
            );
        }

        $this->fields_list['enabled'] = array(
            'title' => $this->l('Enabled', 'QuotationCatalog'),
            'align' => 'text-center',
            'active' => 'enable',
            'type' => 'bool',
            'ajax' => true,
            'orderby' => false,
            'filter_type' => 'bool',
            'filter_key' => 'pq!enabled',
        );

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL) {
            $this->fields_list['shop_name'] = array(
                'title' => $this->l('Store', 'QuotationCatalog'),
                'filter_key' => 'shop!name',
            );
        }

        $this->toolbar_title = $this->l('Quotation Catalog', 'QuotationCatalog');
        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('QuotationCatalog')
        );
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        $id_lang_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ?
        (int) $this->context->shop->id : 'a.id_shop_default';
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
    }

    public function init()
    {
        if (Tools::isSubmit('add' . $this->table)) {
            $this->display = 'add';
        } elseif (Tools::isSubmit('update' . $this->table)) {
            $this->display = 'edit';
        }
        return parent::init();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table . 'Orderby') || Tools::isSubmit($this->table . 'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAdd' . $this->table)) {
            return parent::postProcess();
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            return parent::postProcess();
        } elseif (Tools::isSubmit('submitUpdateEnabled' . $this->table)) {
            RojaProductQuotation::updateEnabled(Tools::getValue('id_product'));
            Roja45QuotationsPro::clearAllCached();
            return parent::postProcess();
        } elseif (Tools::isSubmit('submitBulkenable' . $this->table)) {
            foreach (Tools::getValue($this->table . 'Box') as $id_product) {
                RojaProductQuotation::updateEnabled($id_product, 1);
            }
            Roja45QuotationsPro::clearAllCached();
            Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    'QuotationCatalog',
                    true
                )
            );
        } elseif (Tools::isSubmit('submitBulkenableAllproduct')) {
            $id_shop = null;
            if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ?
                (int) $this->context->shop->id : Configuration::get('PS_SHOP_DEFAULT');
            }

            RojaProductQuotation::enableAll($id_shop);
            Roja45QuotationsPro::clearAllCached();
            Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    'QuotationCatalog',
                    true
                )
            );
        } elseif (Tools::isSubmit('submitBulkdisable' . $this->table)) {
            foreach (Tools::getValue($this->table . 'Box') as $id_product) {
                RojaProductQuotation::updateEnabled($id_product, 0);
            }
            Roja45QuotationsPro::clearAllCached();
            Tools::redirectAdmin(
                $this->context->link->getAdminLink(
                    'QuotationCatalog',
                    true
                )
            );
        } else {
            return parent::postProcess();
        }
    }

    public function ajaxProcessEnableproduct()
    {
        if (RojaProductQuotation::updateEnabled(Tools::getValue('id_product'))) {
            Roja45QuotationsPro::clearAllCached();
            $json = json_encode(array(
                'success' => 1,
                'text' => $this->l('The status has been updated successfully.', 'QuotationCatalog'),
            ));
            die($json);
        } else {
            $json = json_encode(array(
                'success' => 0,
                'text' => $this->l('An error occurred while updating this status.', 'QuotationCatalog'),
            ));
            die($json);
        }
    }

    public function displayUpdateenabledLink($token, $id)
    {
        $tpl = $this->createTemplate('list_action_enable_product.tpl');
        $tpl->assign(array(
            'href' => $this->context->link->getAdminLink(
                'QuotationCatalog',
                true
            ) . '&submitUpdateEnabled' . $this->table . '&' . $this->identifier . '=' . $id,
            'id_product' => $id,
            'token' => $token,
        ));
        return $tpl->fetch();
    }

    public function displayPreviewLink($token, $id)
    {
        $tpl = $this->createTemplate('list_action_preview.tpl');
        $tpl->assign(array(
            'href' => $this->context->link->getProductLink(
                $id,
                null,
                null,
                null,
                $this->context->language->id
            ),
            'id_product' => $id,
        ));
        return $tpl->fetch();
    }
    protected function getShopContextError()
    {
        return '
            <p class="alert alert-danger">' .
        $this->l('Please enable products from the shop context.') .
            '</p>';
    }
}
