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
 * Class AdminEtsSeoSearchAppearanceSitemapController
 *
 * @property Ets_Seo $module
 */
class AdminEtsSeoSearchAppearanceSitemapController extends ModuleAdminController
{
    /**
     * __construct.
     *
     * @return void
     */
    public $priority_options;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'rss_setting' => [
                'title' => $this->l('Sitemap settings'),
                'fields' => $seoDef->fields_config()['sitemap_setting'],
                'icon' => '',
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => [
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->l('You must enable module SEO Audit to configure its features');
        }
    }

    public function renderOptions()
    {
        $this->setPriorityOptions();
        $languages = Language::getLanguages(true);
        $prefixes = [];
        $defaultPrefix = null;
        foreach ($languages as $language) {
            $meta = Meta::getMetaByPage('module-ets_seo-sitemap', $language['id_lang']);
            $prefixes[$language['id_lang']] = $meta ? $meta['url_rewrite'] : 'sitemap';
            if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) {
                $defaultPrefix = $prefixes[$language['id_lang']];
            }
        }
        $this->context->smarty->assign([
            'ets_seo_base_uri' => __PS_BASE_URI__,
            'ets_seo_multilang_activated' => Language::isMultiLanguageActivated($this->context->shop->id),
            'ets_seo_languages' => $languages,
            'prefixes' => $prefixes,
            'defaultPrefix' => $defaultPrefix,
            'ets_seo_baseurl' => $this->context->shop->getBaseURL(true, true),
            'ets_seo_priority_options' => $this->priority_options,
            'ETS_SEO_SITEMAP_OPTION' => explode(',', Configuration::get('ETS_SEO_SITEMAP_OPTION')),
        ]);

        return parent::renderOptions();
    }

    public function postProcess()
    {
        $this->setPriorityOptions();
        $priorityValues = [
            0.0,
            0.1,
            0.2,
            0.3,
            0.4,
            0.5,
            0.6,
            0.7,
            0.8,
            0.9,
            1.0, ];
        $priorityValuesStr = '';
        foreach ($priorityValues as $p) {
            $priorityValuesStr .= number_format($p, 1, '.', '') . ', ';
        }
        if (Tools::isSubmit('submitOptionsconfiguration') || Tools::isSubmit('resetSitemap')) {
            $limit = Tools::getValue('ETS_SEO_PROD_SITEMAP_LIMIT');
            if ($limit || '0' == $limit) {
                if ('0' == $limit) {
                    $this->errors[] = $this->l('The Number product per page in sitemap pagination must be an unsigned integer');
                } elseif (!Validate::isUnsignedInt($limit)) {
                    $this->errors[] = $this->l('The Number product per page in sitemap pagination must be an unsigned integer');
                }
            }
            if (($sitemapOp = Tools::getValue('ETS_SEO_SITEMAP_OPTION')) && is_array($sitemapOp) && Ets_Seo::validateArray($sitemapOp)) {
                $_POST['ETS_SEO_SITEMAP_OPTION'] = implode(',', $sitemapOp);
            }
            foreach ($this->priority_options as $option) {
                if (@$option['changefreg_disable']) {
                    continue;
                }
                $val = (float) Tools::getValue($option['name']);
                Configuration::updateValue($option['name'], $val);
                $changefreq_value = ($changefreq_value = Tools::getValue($option['changefreq_name'])) && Validate::isCleanHtml($changefreq_value) ? $changefreq_value : '';
                Configuration::updateValue($option['changefreq_name'], $changefreq_value);
            }
        }

        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $enable_sitemap = (int) Tools::getValue('ETS_SEO_ENABLE_XML_SITEMAP');
            if ($enable_sitemap) {
                $this->module->setSitemap();
            } else {
                $this->module->removeSitemap();
            }
            $sitemapOp = ($sitemapOp = Tools::getValue('ETS_SEO_SITEMAP_OPTION')) && Validate::isCleanHtml($sitemapOp) ? $sitemapOp : '';
            Configuration::updateValue('ETS_SEO_SITEMAP_OPTION', $sitemapOp);
            $this->module->_clearCache('*');
        }
    }

    public function setPriorityOptions()
    {
        $this->priority_options = [
            'product' => [
                'label' => $this->l('Products'),
                'value' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_PRODUCT'),
                'name' => 'ETS_SEO_SITEMAP_PRIORITY_PRODUCT',
                'changefreq_name' => 'ETS_SEO_SITEMAP_FREQ_PRODUCT',
                'changefreq_value' => Configuration::get('ETS_SEO_SITEMAP_FREQ_PRODUCT'),
            ],
            'category' => [
                'label' => $this->l('Categories'),
                'value' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_CATEGORY'),
                'name' => 'ETS_SEO_SITEMAP_PRIORITY_CATEGORY',
                'changefreq_name' => 'ETS_SEO_SITEMAP_FREQ_CATEGORY',
                'changefreq_value' => Configuration::get('ETS_SEO_SITEMAP_FREQ_CATEGORY'),
            ],
            'cms' => [
                'label' => $this->l('CMS'),
                'value' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_CMS'),
                'name' => 'ETS_SEO_SITEMAP_PRIORITY_CMS',
                'changefreq_name' => 'ETS_SEO_SITEMAP_FREQ_CMS',
                'changefreq_value' => Configuration::get('ETS_SEO_SITEMAP_FREQ_CMS'),
            ],
            'cms_category' => [
                'label' => $this->l('CMS categories'),
                'value' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_CMS_CATEGORY'),
                'name' => 'ETS_SEO_SITEMAP_PRIORITY_CMS_CATEGORY',
                'changefreq_name' => 'ETS_SEO_SITEMAP_FREQ_CMS_CATEGORY',
                'changefreq_value' => Configuration::get('ETS_SEO_SITEMAP_FREQ_CMS_CATEGORY'),
            ],
            'supplier' => [
                'label' => $this->l('Suppliers'),
                'value' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_SUPPLIER'),
                'name' => 'ETS_SEO_SITEMAP_PRIORITY_SUPPLIER',
                'changefreq_name' => 'ETS_SEO_SITEMAP_FREQ_SUPPLIER',
                'changefreq_value' => Configuration::get('ETS_SEO_SITEMAP_FREQ_SUPPLIER'),
            ],
            'manufacturer' => [
                'label' => $this->l('Brands (manufacturers)'),
                'value' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_MANUFACTURER'),
                'name' => 'ETS_SEO_SITEMAP_PRIORITY_MANUFACTURER',
                'changefreq_name' => 'ETS_SEO_SITEMAP_FREQ_MANUFACTURER',
                'changefreq_value' => Configuration::get('ETS_SEO_SITEMAP_FREQ_MANUFACTURER'),
            ],
            'meta' => [
                'label' => $this->l('Other pages'),
                'value' => Configuration::get('ETS_SEO_SITEMAP_PRIORITY_META'),
                'name' => 'ETS_SEO_SITEMAP_PRIORITY_META',
                'changefreq_name' => 'ETS_SEO_SITEMAP_FREQ_META',
                'changefreq_value' => Configuration::get('ETS_SEO_SITEMAP_FREQ_META'),
            ],
            'blog' => [
                'label' => $this->l('Blog pages'),
                'changefreg_disable' => true,
            ],
        ];
        if(!Module::isEnabled('ybc_blog'))
        {
            unset($this->priority_options['blog']);
        }
    }
}
