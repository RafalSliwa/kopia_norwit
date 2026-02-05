<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AdminEtsSeoSearchAppearanceContentTypeController
 *
 * @property Ets_Seo $module;
 */
class AdminEtsSeoSearchAppearanceContentTypeController extends ModuleAdminController
{
    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'product' => [
                'title' => $this->l('Products'),
                'fields' => $seoDef->fields_config()['search_content_type_product'],
                'icon' => '',
                'short_code' => [
                    'title' => $this->module->getMetaCodeTemplate('product', true),
                    'desc' => $this->module->getMetaCodeTemplate('product', false),
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'category' => [
                'title' => $this->l('Category pages'),
                'fields' => $seoDef->fields_config()['search_content_type_category'],
                'icon' => '',
                'short_code' => [
                    'title' => $this->module->getMetaCodeTemplate('category', true),
                    'desc' => $this->module->getMetaCodeTemplate('category', false),
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'cms' => [
                'title' => $this->l('CMS pages'),
                'fields' => $seoDef->fields_config()['search_content_type_cms'],
                'icon' => '',
                'short_code' => [
                    'title' => $this->module->getMetaCodeTemplate('cms', true),
                    'desc' => $this->module->getMetaCodeTemplate('cms', false),
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'cms_category' => [
                'title' => $this->l('Cms category pages'),
                'fields' => $seoDef->fields_config()['search_content_type_cms_cate'],
                'icon' => '',
                'short_code' => [
                    'title' => $this->module->getMetaCodeTemplate('cms_category', true),
                    'desc' => $this->module->getMetaCodeTemplate('cms_category', false),
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'manufacturer' => [
                'title' => $this->l('Brand (manufacturer) pages'),
                'fields' => $seoDef->fields_config()['search_content_type_manufacturer'],
                'icon' => '',
                'short_code' => [
                    'title' => $this->module->getMetaCodeTemplate('manufacturer', true),
                    'desc' => $this->module->getMetaCodeTemplate('manufacturer', false),
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
            'supplier' => [
                'title' => $this->l('Supplier pages'),
                'icon' => '',
                'short_code' => [
                    'title' => $this->module->getMetaCodeTemplate('supplier', true),
                    'desc' => $this->module->getMetaCodeTemplate('supplier', false),
                ],
                'fields' => $seoDef->fields_config()['search_content_type_supplier'],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->l('You must enable module SEO Audit to configure its features');
        }
    }

    public function renderOptions()
    {
        return parent::renderOptions();
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->module->_clearCache('*');
        }
    }
}
