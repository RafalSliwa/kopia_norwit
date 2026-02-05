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
 * Class AdminEtsSeoGeneralDashboardController
 *
 * @property \Ets_Seo $module
 * @property \Context|\ContextCore $context
 *
 * @mixin \ModuleAdminControllerCore
 */
class AdminEtsSeoGeneralDashboardController extends ModuleAdminController
{
    public $pageTypes;

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->fields_options = [
            'dashboard' => [
                'title' => $this->module->l('Dashboard'),
                'info' => $this->module->l('Coming soon...'),
                'fields' => [],
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features');
        }

        $this->pageTypes = [
            'product' => $this->module->l('Product pages'),
            'category' => $this->module->l('Product category pages'),
            'cms' => $this->module->l('CMS pages'),
            'cms_category' => $this->module->l('CMS category pages'),
            'manufacturer' => $this->module->l('Brand pages'),
            'supplier' => $this->module->l('Supplier pages'),
            'meta' => $this->module->l('Other pages'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function initContent()
    {
        parent::initContent();
        $this->module->getJsDefHelper()->addBo('transMsg.analyzePageRequire', $this->module->l('At least a page is required to analyze.', __FILE__));
    }

    public function renderOptions()
    {
        $tplPath = 'dashboard.tpl';
        $cacheId = $this->module->_getCacheId(['dashboard' => date('Ymd')]);
        $this->module->_clearQueuedCache($tplPath);
        if (!$this->module->isCached($tplPath, $cacheId)) {
            $totalIndexFollow = EtsSeoAnalysis::getTotalIndexFollow();
            $totalMetaIndex = EtsSeoAnalysis::getTotalMetaIndex();
            $chart_index = [];
            if ((int) $totalIndexFollow['index'] || (int) $totalIndexFollow['noindex']) {
                $chart_index = [
                    [
                        'label' => $this->module->l('Index'),
                        'value' => $totalIndexFollow['index'],
                    ],
                    [
                        'label' => $this->module->l('No index'),
                        'value' => $totalIndexFollow['noindex'],
                    ],
                ];
            }

            $chart_follow = [];
            if ((int) $totalIndexFollow['follow'] || (int) $totalIndexFollow['nofollow']) {
                $chart_follow = [
                    [
                        'label' => $this->module->l('Follow'),
                        'value' => $totalIndexFollow['follow'],
                    ],
                    [
                        'label' => $this->module->l('No follow'),
                        'value' => $totalIndexFollow['nofollow'],
                    ],
                ];
            }
            $seo_score = [
                'bad' => $totalIndexFollow['seo_score_bad'],
                'na' => $totalIndexFollow['seo_score_na'],
                'good' => $totalIndexFollow['seo_score_good'],
                'noanalysis' => $totalMetaIndex['noanalysis'],
            ];
            $readability_score = [
                'bad' => $totalIndexFollow['readability_score_bad'],
                'na' => $totalIndexFollow['readability_score_na'],
                'good' => $totalIndexFollow['readability_score_good'],
            ];
            $meta_data = [
                [
                    'label' => $this->module->l('Completed'),
                    'value' => $totalMetaIndex['hasmeta'],
                ],
                [
                    'label' => $this->module->l('Not completed'),
                    'value' => $totalMetaIndex['nometa'],
                ],
            ];

            $page_analysis = $this->pageAnalysis();

            foreach ($page_analysis as $pk => &$item) {
                foreach ($item as &$page) {
                    foreach ($this->pageTypes as $key => $type) {
                        $page['values'][] = [
                            'label' => $type,
                            'type' => $key,
                            'value' => 'noanalysis' !== $page['type'] ? $totalIndexFollow['pages'][$key][$pk][$page['type']] : $totalMetaIndex['pages'][$key][$pk]['noanalysis'],
                        ];
                    }
                }
            }
            unset($item, $page);
            $this->context->smarty->assign(
                [
                    'ets_seo_link_dashboard_js' => __PS_BASE_URI__ . 'modules/' . $this->module->name . '/views/js/dashboard.js',
                    'ets_seo_data_dashboard' => [
                        'chart_index' => $chart_index,
                        'chart_follow' => $chart_follow,
                        'total_index' => $totalIndexFollow['index'],
                        'total_noindex' => $totalIndexFollow['noindex'],
                        'total_follow' => $totalIndexFollow['follow'],
                        'total_nofollow' => $totalIndexFollow['nofollow'],
                        'seo_score' => $seo_score,
                        'readability_score' => $readability_score,
                        'meta_data' => $meta_data,
                        'chart_page_analytics' => $page_analysis,
                    ],
                    'ets_seo_checklist' => $this->getSeoChecklist(),
                    'multi_lang_enable' => Language::isMultiLanguageActivated(),
                    'txt_multilang' => $this->module->l('These numbers of web page have been multiplied with the number of available languages due to your multi-language mode.'),
                ]
            );
        }

        return $this->module->display($this->module->getLocalPath(), $tplPath, $cacheId);
    }

    protected function pageAnalysis()
    {
        return [
            'readability_score' => [
                [
                    'key' => $this->module->l('Excellent'),
                    'color' => '#32C020',
                    'type' => 'good',
                    'values' => [],
                ],
                [
                    'key' => $this->module->l('Acceptable'),
                    'color' => '#FF8E37',
                    'type' => 'na',
                    'values' => [],
                ],
                [
                    'key' => $this->module->l('Not good'),
                    'color' => '#FF5D5E',
                    'type' => 'bad',
                    'values' => [],
                ],
                [
                    'key' => $this->module->l('No analysis'),
                    'color' => '#6C868E',
                    'type' => 'noanalysis',
                    'values' => [],
                ],
            ],
            'seo_score' => [
                [
                    'key' => $this->module->l('Excellent'),
                    'color' => '#32C020',
                    'type' => 'good',
                    'values' => [],
                ],
                [
                    'key' => $this->module->l('Acceptable'),
                    'color' => '#FF8E37',
                    'type' => 'na',
                    'values' => [],
                ],
                [
                    'key' => $this->module->l('Not good'),
                    'color' => '#FF5D5E',
                    'type' => 'bad',
                    'values' => [],
                ],
                [
                    'key' => $this->module->l('No analysis'),
                    'color' => '#6C868E',
                    'type' => 'noanalysis',
                    'values' => [],
                ],
            ],
        ];
    }

    protected function getSeoChecklist()
    {
        $webMasterToolsStatus = Configuration::get('ETS_SEO_BAIDU_VERIFY_CODE')
            || Configuration::get('ETS_SEO_BING_VERIFY_CODE')
            || Configuration::get('ETS_SEO_GOOGLE_VERIFY_CODE')
            || Configuration::get('ETS_SEO_YANDEX_VERIFY_CODE')
            || (int) Configuration::get('ETS_SEO_VERIFIED_BY_USING_OTHER_METHODS');

        $robotFileGenerated = file_exists(_PS_ROOT_DIR_ . '/robots.txt') || file_exists(_PS_ROOT_DIR_ . '/_robots.txt');

        return [
            [
                'title' => $this->module->l('Enable friendly URL'),
                'status' => (int) Configuration::get('PS_REWRITING_SETTINGS') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminMeta', true),
            ],
            [
                'title' => $this->module->l('Remove ID (numbers) in URL'),
                'status' => (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminMeta', true),
            ],
            [
                'title' => $this->module->l('Enable old link to new link redirects'),
                'status' => (int) Configuration::get('ETS_SEO_ENABLE_REDRECT_NOTFOUND') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminMeta', true),
                'hide' => (int) Configuration::get('ETS_SEO_ENABLE_REMOVE_ID_IN_URL') ? 0 : 1,
            ],
            [
                'title' => $this->module->l('Enable SSL (HTTPS) on all pages'),
                'status' => (int) Configuration::get('PS_SSL_ENABLED') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminPreferences', true),
            ],
            [
                'title' => $this->module->l('Enable RSS'),
                'status' => (int) Configuration::get('ETS_SEO_RSS_ENABLE') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceRSS', true),
            ],
            [
                'title' => $this->module->l('Enable sitemap'),
                'status' => (int) Configuration::get('ETS_SEO_ENABLE_XML_SITEMAP') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceSitemap', true),
            ],
            [
                'title' => $this->module->l('Verify site ownership on webmaster tools'),
                'status' => $webMasterToolsStatus,
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceGeneral', true),
            ],
            [
                'title' => $this->module->l('robots.txt file created'),
                'status' => $robotFileGenerated,
                'link' => $this->context->link->getAdminLink('AdminEtsSeoFileEditor', true),
            ],
            [
                'title' => $this->module->l('Debug mode is off'),
                'status' => !_PS_MODE_DEV_ ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminPerformance', true),
            ],
            [
                'title' => $this->module->l('Maintenance mode is off '),
                'status' => (int) Configuration::get('PS_SHOP_ENABLE') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminMaintenance', true),
            ],
            [
                'title' => $this->module->l('Cache is enabled'),
                'status' => (int) Configuration::get('PS_SMARTY_CACHE') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminPerformance', true),
            ],
            [
                'title' => $this->module->l('Template compilation is set to "Never"'),
                'status' => 0 == (int) Configuration::get('PS_SMARTY_FORCE_COMPILE') ? 1 : 0,
                'link' => $this->context->link->getAdminLink('AdminPerformance', true),
            ],
            [
                'title' => $this->module->l('Enable Super Speed - The best speed optimization module'),
                'status' => Module::isInstalled('ets_superspeed') && Module::isEnabled('ets_superspeed') ? 1 : 0,
                'is_module' => true,
                'is_installed' => Module::isInstalled('ets_superspeed') ? true : false,
                'link' => !Module::isInstalled('ets_superspeed') ? 'https://addons.prestashop.com/en/website-performance/44977-super-speed-incredibly-fast-gtmetrix-optimization.html' : (!Module::isEnabled('ets_superspeed') ? $this->context->link->getAdminLink('AdminModulesManage') : $this->context->link->getAdminLink('AdminModules') . '&configure=ets_superspeed&module_name=ets_superspeed'),
            ],
            [
                'title' => $this->module->l('Enable BLOG - The best blog module for Prestashop'),
                'status' => Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog') ? 1 : 0,
                'is_module' => true,
                'is_installed' => Module::isInstalled('ybc_blog') ? true : false,
                'link' => !Module::isInstalled('ybc_blog') ? 'https://addons.prestashop.com/en/blog-forum-new/25908-blog.html' : (!Module::isEnabled('ybc_blog') ? $this->context->link->getAdminLink('AdminModulesManage') : $this->context->link->getAdminLink('AdminModules') . '&configure=ybc_blog&module_name=ybc_blog'),
            ],
        ];
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('getNoAnalysisPage')) {
            $pageType = Tools::getValue('pageType');
            $types = ['product', 'category', 'cms', 'cms_category', 'meta', 'manufacturer', 'supplier'];
            if (in_array($pageType, $types, true)) {
                if ('cms' === $pageType) {
                    $data = [
                        'cms' => EtsSeoAnalysis::countNoAnalysisPageByType($pageType),
                        'cms_category' => EtsSeoAnalysis::countNoAnalysisPageByType('cms_category'),
                        'categoryBtnText' => $this->module->l('Analysis missing categories', __FILE__),
                    ];
                } else {
                    $data = EtsSeoAnalysis::countNoAnalysisPageByType($pageType);
                }
                exit(json_encode(['ok' => true, 'data' => $data]));
            }
            exit(json_encode(['ok' => false, 'hasErrors' => true, 'message' => $this->module->l('Invalid page type selection.', __FILE__)]));
        }
        if (Tools::isSubmit('etsSeoGetAnalysisModal')) {
            $totalMetaIndex = EtsSeoAnalysis::getTotalMetaIndex();
            $pk = 'seo_score';
            $dataNoAnalysis = [];
            foreach ($this->pageTypes as $key => $type) {
                $dataNoAnalysis[$key] = $totalMetaIndex['pages'][$key][$pk]['noanalysis'];
            }
            unset($item);
            $totalPages = 0;
            foreach ($dataNoAnalysis as $value) {
                $totalPages += (int) $value;
            }

            $this->context->smarty->assign([
                'dataNoAnalysis' => $dataNoAnalysis,
                'listPages' => $this->pageTypes,
                'totalPage' => (int) $totalPages,
            ]);
            exit(json_encode([
                'success' => true,
                'modal_html' => $this->module->fetch(_PS_MODULE_DIR_ . 'ets_seo/views/templates/admin/modal_select_analysis.tpl'),
            ]));
        }
        $tplPath = 'dashboard.tpl';
        if ((int) Tools::isSubmit('etsSeoAnalysisPages')) {
            $dataPages = ($dataPages = Tools::getValue('dataPages')) && Ets_Seo::validateArray($dataPages) ? $dataPages : [];
            if (!$dataPages || !is_array($dataPages)) {
                exit(json_encode([
                    'success' => true,
                    'stop' => 1,
                    'message' => $this->module->l('Analysis successfully'),
                ]));
            }
            $data = EtsSeoAnalysis::getInstance()->analysisPages($dataPages);
            exit(json_encode([
                'success' => true,
                'data' => $data,
                'stop' => 0,
            ]));
        }

        if ((int) Tools::isSubmit('etsSeoSaveDataAnalysis')) {
            $this->module->_addToQueueClearCache($tplPath);
            $scoreData = ($scoreData = Tools::getValue('scoreData')) && Ets_Seo::validateArray($scoreData) ? $scoreData : [];
            if (!$scoreData || !isset($scoreData['page_type']) || !isset($scoreData['score'])) {
                exit(json_encode([
                    'success' => true,
                    'message' => $this->module->l('Analysis successfully'),
                    'stop' => 1,
                ]));
            }
            EtsSeoAnalysis::getInstance()->updateDataAnalysis($scoreData['page_type'], $scoreData['score']);
            exit(json_encode([
                'success' => true,
                'message' => $this->module->l('Success'),
                'pages' => isset($scoreData['pages']) ? $scoreData['pages'] : [],
                'stop' => isset($scoreData['stop']) && (int) $scoreData['stop'] ? 1 : 0,
            ]));
        }
    }
}
