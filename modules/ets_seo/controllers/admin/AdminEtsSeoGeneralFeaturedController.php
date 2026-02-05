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

class AdminEtsSeoGeneralFeaturedController extends ModuleAdminController
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
            'featured' => [
                'title' => $this->l('Features'),
                'description' => $this->l('ETS SEO comes with a lot of features. You can enable / disable some of them below. Clicking the question mark gives more information about the feature.'),
                'fields' => $seoDef->fields_config()['general_featured'],
                'icon' => '',
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->l('You must enable module SEO Audit to configure its features');
        }
    }

    /**
     * renderForm.
     *
     * @return void
     */
    public function renderOptions()
    {
        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = [
                'save' => [
                    'href' => '#',
                    'desc' => $this->l('Save'),
                ],
            ];
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    /**
     * postProcess.
     *
     * @return void
     */
    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            Tools::clearAllCache();
        }
    }
}
