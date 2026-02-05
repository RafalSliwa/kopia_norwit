<?php
/**
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

if (!defined('_PS_VERSION_')) {
    exit;
}
use AdvancedSearch\AdvancedSearchProductListingFrontController;
use AdvancedSearch\Core;
use AdvancedSearch\Models\Criterion;
use AdvancedSearch\Models\CriterionGroup;
use AdvancedSearch\Models\Search;
use AdvancedSearch\Models\Seo;
use AdvancedSearch\SearchEngineUtils;
class pm_advancedsearch4seoModuleFrontController extends AdvancedSearchProductListingFrontController
{
    protected $seoUrl;
    protected $pageNb = 1;
    protected $originalCriterions;
    protected $seoPageInstance;
    protected $indexState = 'index';
    public function init()
    {
        parent::init();
        $this->php_self = 'module-' . _PM_AS_MODULE_NAME_ . '-seo';
        $this->setSEOTags();
        $this->setProductFilterList();
        $this->setSmartyVars();
        if (!empty($this->seoPageInstance->id_criterion_master)) {
            $masterCriterion = new Criterion($this->seoPageInstance->id_criterion_master, $this->idSearch);
            if (Validate::isLoadedObject($masterCriterion)) {
                $masterCriterionGroup = new CriterionGroup($masterCriterion->id_criterion_group, $this->idSearch);
                if (Validate::isLoadedObject($masterCriterionGroup)) {
                    if ($masterCriterionGroup->criterion_group_type == 'category') {
                        $this->currentCategoryObject = new Category((int)current($masterCriterion->id_criterion_linked), (int)$this->context->language->id);
                    } elseif ((version_compare(_PS_VERSION_, '1.7.7.0', '<') || Configuration::get('PS_DISPLAY_MANUFACTURERS')) && $masterCriterionGroup->criterion_group_type == 'manufacturer') {
                        $this->currentManufacturerObject = new Manufacturer((int)current($masterCriterion->id_criterion_linked), (int)$this->context->language->id);
                    } elseif (Configuration::get('PS_DISPLAY_SUPPLIERS') && $masterCriterionGroup->criterion_group_type == 'supplier') {
                        $this->currentSupplierObject = new Supplier((int)current($masterCriterion->id_criterion_linked), (int)$this->context->language->id);
                    }
                }
            }
        }
        if (!headers_sent()) {
            header('Link: <' . $this->getCanonicalURL() . '>; rel="canonical"', true);
        }
        if (Tools::getIsset('from-xhr')) {
            $this->doProductSearch('');
        } else {
            $this->template = 'module:' . _PM_AS_MODULE_NAME_ . '/views/templates/front/' . $this->module->getPrestaShopTemplateVersion() . '/seo-page.tpl';
        }
    }
    protected function redirectToSeoPageIndex()
    {
        $seoObj = new Seo($this->idSeo, $this->context->language->id);
        if (Validate::isLoadedObject($seoObj)) {
            Tools::redirect($this->context->link->getModuleLink(_PM_AS_MODULE_NAME_, 'seo', [
                'id_seo' => (int)$seoObj->id,
                'seo_url' => $seoObj->seo_url,
            ], null, (int)$this->context->language->id));
        } else {
            Tools::redirect('index');
        }
    }
    protected function setSEOTags()
    {
        $this->idSeo = (int)Tools::getValue('id_seo');
        $this->seoUrl = strip_tags(Tools::getValue('seo_url'));
        $this->pageNb = (int)Tools::getValue('page', 1);
        if (empty($this->seoUrl) || empty($this->idSeo)) {
            Tools::redirect('404');
        }
        $resultSeoUrl = Seo::getSeoSearchByIdSeo((int)$this->idSeo, (int)$this->context->language->id);
        if (!$resultSeoUrl) {
            Tools::redirect('404');
        }
        $this->seoPageInstance = new Seo($this->idSeo, $this->context->language->id);
        $this->idSearch = (int)$resultSeoUrl[0]['id_search'];
        $this->searchInstance = new Search((int)$this->idSearch, (int)$this->context->language->id);
        if (!Validate::isLoadedObject($this->searchInstance)) {
            Tools::redirect('404');
        }
        if (!$this->searchInstance->isAssociatedToShop()) {
            Tools::redirect('404');
        }
        if ($resultSeoUrl[0]['deleted']) {
            if (!headers_sent()) {
                header('Status: 301 Moved Permanently', false, 301);
            }
            Tools::redirect('index');
        }
        if (!$this->searchInstance->active) {
            if (!headers_sent()) {
                header('Status: 307 Temporary Redirect', false, 307);
            }
            Tools::redirect('index');
        }
        $seoUrlCheck = current(explode('/', $this->seoUrl));
        if ($resultSeoUrl[0]['seo_url'] != $seoUrlCheck) {
            if (!headers_sent()) {
                header('Status: 301 Moved Permanently', false, 301);
            }
            $this->redirectToSeoPageIndex();
            die;
        }
        $hasPriceCriterionGroup = false;
        if (is_array($this->criterionsList) && count($this->criterionsList)) {
            $selected_criteria_groups_type = SearchEngineUtils::getCriterionGroupsTypeAndDisplay((int)$this->idSearch, array_keys($this->criterionsList));
            if (is_array($selected_criteria_groups_type) && count($selected_criteria_groups_type)) {
                foreach ($selected_criteria_groups_type as $criterionGroup) {
                    if ($criterionGroup['criterion_group_type'] == 'price') {
                        $hasPriceCriterionGroup = true;
                        break;
                    }
                }
            }
        }
        if ($hasPriceCriterionGroup && $resultSeoUrl[0]['id_currency'] && $this->context->cookie->id_currency != (int)$resultSeoUrl[0]['id_currency']) {
            $this->context->cookie->id_currency = $resultSeoUrl[0]['id_currency'];
            if (!headers_sent()) {
                header('Refresh: 1; URL=' . $_SERVER['REQUEST_URI']);
            }
            die;
        }
        $criteria = Core::decodeCriteria($resultSeoUrl[0]['criteria']);
        if (is_array($criteria) && count($criteria)) {
            $this->criterionsList = PM_AdvancedSearch4::getArrayCriteriaFromSeoArrayCriteria($criteria);
            $this->criterionsList = SearchEngineUtils::cleanArrayCriterion($this->criterionsList);
        }
        $searchQuery = implode('/', array_slice(explode('/', $this->seoUrl), 1));
        $criterionsList = SearchEngineUtils::getCriterionsFromURL($this->idSearch, $searchQuery);
        if (is_array($criterionsList) && count($criterionsList)) {
            if (count($this->criterionsList) > 0) {
                $arrayDiff = $criterionsList;
                foreach ($arrayDiff as $arrayDiffKey => $arrayDiffRow) {
                    if (isset($this->criterionsList[$arrayDiffKey]) && $this->criterionsList[$arrayDiffKey] == $arrayDiffRow) {
                        unset($arrayDiff[$arrayDiffKey]);
                    }
                }
                if (is_array($arrayDiff) && count($arrayDiff)) {
                    $this->indexState = 'noindex';
                }
                unset($arrayDiff);
            } else {
                $this->indexState = 'noindex';
            }
        }
        $this->originalCriterions = $this->criterionsList;
        $this->criterionsList += $criterionsList;
        $this->context->smarty->assign([
            'as_is_seo_page' => true,
            'as_cross_links' => Seo::getCrossLinksSeo((int)$this->context->language->id, $resultSeoUrl[0]['id_seo']),
        ]);
    }
    protected function setSmartyVars()
    {
        $variables = $this->getProductSearchVariables();
        if ($this->pageNb < 1 || ($this->pageNb > 1 && empty($variables['products']))) {
            $this->redirectToSeoPageIndex();
        }
        $seoPageDescription = $this->seoPageInstance->description;
        $seoPageFooterDescription = $this->seoPageInstance->footer_description;
        if (Module::isEnabled('appagebuilder')) {
            $appagebuilder = Module::getInstanceByName('appagebuilder');
            if (method_exists($appagebuilder, 'buildShortCode')) {
                $seoPageDescription = $appagebuilder->buildShortCode($seoPageDescription);
                $seoPageFooterDescription = $appagebuilder->buildShortCode($seoPageFooterDescription);
            }
        }
        $this->context->smarty->assign([
            'listing' => $variables,
            'id_search' => $this->idSearch,
            'as_seo_description' => $seoPageDescription,
            'as_seo_footer_description' => $seoPageFooterDescription,
            'as_seo_title' => $this->seoPageInstance->title,
            'as_see_also_txt' => $this->module->l('See also', 'seo'),
        ]);
    }
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->seoPageInstance->title,
            'url' => $this->context->link->getModuleLink(_PM_AS_MODULE_NAME_, 'seo', [
                'id_seo' => (int)$this->seoPageInstance->id,
                'seo_url' => $this->seoPageInstance->seo_url,
            ], null, (int)$this->context->language->id),
        ];
        return $breadcrumb;
    }
    public function getIdSeo()
    {
        return $this->idSeo;
    }
    public function getSeoPage()
    {
        return $this->seoPageInstance;
    }
    public function getListingLabel()
    {
        return $this->seoPageInstance->title;
    }
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['meta']['robots'] = $this->indexState;
        $page['meta']['title'] = $this->seoPageInstance->meta_title;
        $page['meta']['description'] = $this->seoPageInstance->meta_description;
        $page['meta']['keywords'] = $this->seoPageInstance->meta_keywords;
        $page['page_name'] = 'advancedsearch-seo-' . (int)$this->idSeo;
        $page['body_classes']['advancedsearch-seo'] = true;
        $page['body_classes']['advancedsearch-seo-' . (int)$this->idSeo] = true;
        return $page;
    }
}
