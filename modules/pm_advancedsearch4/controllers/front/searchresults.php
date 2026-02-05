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

use AdvancedSearch\Models\Search;
use AdvancedSearch\SearchEngineUtils;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use AdvancedSearch\AdvancedSearchProductListingFrontController;
if (!defined('_PS_VERSION_')) {
    exit;
}
class pm_advancedsearch4searchresultsModuleFrontController extends AdvancedSearchProductListingFrontController
{
    protected $currentIdCategory;
    protected $currentIdManufacturer;
    protected $currentIdSupplier;
    protected $currentIdCms;
    public function init()
    {
        parent::init();
        $this->php_self = 'module-' . _PM_AS_MODULE_NAME_ . '-searchresults';
        if (!headers_sent()) {
            header('X-Robots-Tag: noindex', true);
        }
        $this->idSearch = (int)Tools::getValue('id_search');
        $this->searchInstance = new Search((int)$this->idSearch, (int)$this->context->cookie->id_lang);
        if (!Validate::isLoadedObject($this->searchInstance)) {
            Tools::redirect('404');
        }
        if (!$this->searchInstance->isAssociatedToShop()) {
            Tools::redirect('404');
        }
        if (!$this->searchInstance->active) {
            if (!headers_sent()) {
                header('Status: 307 Temporary Redirect', false, 307);
            }
            Tools::redirect('index');
        }
        if (Tools::getValue('as4_from') == 'category') {
            $this->currentIdCategory = (int)SearchEngineUtils::getCurrentCategory();
            if (empty($this->currentIdCategory)) {
                Tools::redirect('404');
            }
        } elseif (Tools::getValue('as4_from') == 'manufacturer') {
            $this->currentIdManufacturer = (int)SearchEngineUtils::getCurrentManufacturer();
            if (empty($this->currentIdManufacturer)) {
                Tools::redirect('404');
            }
        } elseif (Tools::getValue('as4_from') == 'supplier') {
            $this->currentIdSupplier = (int)SearchEngineUtils::getCurrentSupplier();
            if (empty($this->currentIdSupplier)) {
                Tools::redirect('404');
            }
        } elseif (Tools::getValue('as4_from') == 'cms') {
            $this->currentIdCms = (int)SearchEngineUtils::getCurrentCMS();
            if (empty($this->currentIdCms)) {
                Tools::redirect('404');
            }
        }
        $this->setCriterions();
        $this->setSmartyVars();
        if (Tools::getValue('order')) {
            try {
                $selectedSortOrder = SortOrder::newFromString(trim(Tools::getValue('order')));
            } catch (Exception $e) {
                $fixedSearchUrl = $this->rewriteOrderParameter();
                if (!headers_sent()) {
                    // Note Team Validation - Tools::redirect does not support replacing headers
                    header('Location:' . $fixedSearchUrl, true, 301);
                }
            }
        }
        if (Tools::getIsset('from-xhr')) {
            $this->doProductSearch('');
        } else {
            $this->template = 'module:' . _PM_AS_MODULE_NAME_ . '/views/templates/front/' . $this->module->getPrestaShopTemplateVersion() . '/search-results.tpl';
        }
    }
    protected function rewriteOrderParameter()
    {
        $defaultSearchEngineOrderBy = SearchEngineUtils::getOrderByValue($this->getSearchEngine());
        $defaultSearchEngineOrderWay = SearchEngineUtils::getOrderWayValue($this->getSearchEngine());
        $selectedSortOrder = new SortOrder('product', $defaultSearchEngineOrderBy, $defaultSearchEngineOrderWay);
        return SearchEngineUtils::generateURLFromCriterions($this->idSearch, $this->criterionsList, null, ['order' => $selectedSortOrder->toString()]);
    }
    protected function setCriterions()
    {
        $currentSearchInstance = $this->getSearchEngine();
        if (!is_object($currentSearchInstance)) {
            return;
        }
        $searchQuery = trim(strip_tags(Tools::getValue('as4_sq')));
        if (!empty($searchQuery)) {
            $this->criterionsList = SearchEngineUtils::getCriterionsFromURL($this->idSearch, $searchQuery);
            if ($currentSearchInstance->filter_by_emplacement) {
                $criterionsFromEmplacement = SearchEngineUtils::getCriteriaFromEmplacement($currentSearchInstance->id);
                foreach ($criterionsFromEmplacement as $idCriterionGroup => $idCriterionList) {
                    if (!isset($this->criterionsList[$idCriterionGroup])) {
                        $this->criterionsList[$idCriterionGroup] = $idCriterionList;
                    } else {
                        $this->criterionsList[$idCriterionGroup] = $this->criterionsList[$idCriterionGroup] + $idCriterionList;
                    }
                }
            }
            $this->criterionsList = SearchEngineUtils::cleanArrayCriterion($this->criterionsList);
            $ignoreNoCriterions = false;
            if (!count($this->criterionsList) && empty($currentSearchInstance->filter_by_emplacement)) {
                $ignoreNoCriterions = true;
            }
            if (!$ignoreNoCriterions && !count($this->criterionsList)) {
                if (!Tools::getIsset('from-xhr') && !Tools::getIsset('order') && !Tools::getIsset('page')) {
                    Tools::redirect('404');
                }
            } else {
                if (!headers_sent()) {
                    header('Link: <' . SearchEngineUtils::generateURLFromCriterions($this->idSearch, $this->criterionsList) . '>; rel="canonical"', true);
                }
            }
        } else {
            if ($currentSearchInstance->filter_by_emplacement) {
                $criterionsFromEmplacement = SearchEngineUtils::getCriteriaFromEmplacement($currentSearchInstance->id);
                foreach ($criterionsFromEmplacement as $idCriterionGroup => $idCriterionList) {
                    if (!isset($this->criterionsList[$idCriterionGroup])) {
                        $this->criterionsList[$idCriterionGroup] = $idCriterionList;
                    } else {
                        $this->criterionsList[$idCriterionGroup] = $this->criterionsList[$idCriterionGroup] + $idCriterionList;
                    }
                }
                $this->criterionsList = SearchEngineUtils::getCriteriaFromEmplacement($currentSearchInstance->id);
                $this->criterionsList = SearchEngineUtils::cleanArrayCriterion($this->criterionsList);
                if (count($this->criterionsList)) {
                    if (!headers_sent()) {
                        header('Link: <' . SearchEngineUtils::generateURLFromCriterions($this->idSearch, $this->criterionsList) . '>; rel="canonical"', true);
                    }
                }
            }
        }
    }
    protected function getImage($object, $id_image)
    {
        $retriever = new ImageRetriever(
            $this->context->link
        );
        return $retriever->getImage($object, $id_image);
    }
    protected function getTemplateVarCategory()
    {
        $currentCategoryObject = $this->currentCategoryObject;
        $category = $this->objectPresenter->present($currentCategoryObject);
        $category['image'] = $this->getImage(
            $currentCategoryObject,
            (int)$currentCategoryObject->id_image
        );
        return $category;
    }
    protected function getTemplateVarSubCategories()
    {
        $currentCategoryObject = $this->currentCategoryObject;
        return array_map(function (array $category) {
            $object = new Category(
                $category['id_category'],
                $this->context->language->id
            );
            $category['image'] = $this->getImage(
                $object,
                (int)$object->id_image
            );
            $category['url'] = $this->context->link->getCategoryLink(
                $category['id_category'],
                $category['link_rewrite']
            );
            return $category;
        }, $currentCategoryObject->getSubCategories($this->context->language->id));
    }
    protected function setSmartyVars()
    {
        $this->module->setProductFilterContext();
        if (!empty($this->currentIdCategory)) {
            $this->currentCategoryObject = new Category($this->currentIdCategory, $this->context->language->id);
        }
        if (!empty($this->currentIdManufacturer) && (version_compare(_PS_VERSION_, '1.7.7.0', '<') || Configuration::get('PS_DISPLAY_MANUFACTURERS'))) {
            $this->currentManufacturerObject = new Manufacturer($this->currentIdManufacturer, $this->context->language->id);
        }
        if (!empty($this->currentIdSupplier) && Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            $this->currentSupplierObject = new Supplier($this->currentIdSupplier, $this->context->language->id);
        }
        if (!empty($this->currentIdCms)) {
            $this->currentCmsObject = new CMS($this->currentIdCms, $this->context->language->id);
        }
        $currentSearchInstance = $this->getSearchEngine();
        if (!empty($this->currentIdCategory) && !empty($currentSearchInstance->keep_category_information)) {
            $this->context->smarty->assign([
                'category' => $this->getTemplateVarCategory(),
                'subcategories' => $this->getTemplateVarSubCategories(),
            ]);
        }
        $seoDescription = $currentSearchInstance->description;
        if (Module::isEnabled('appagebuilder')) {
            $appagebuilder = Module::getInstanceByName('appagebuilder');
            if (method_exists($appagebuilder, 'buildShortCode')) {
                $seoDescription = $appagebuilder->buildShortCode($seoDescription);
            }
        }
        $variables = $this->getProductSearchVariables();
        $this->context->smarty->assign([
            'listing' => $variables,
            'id_search' => $this->idSearch,
            'as_seo_description' => $seoDescription,
            'as_seo_title' => $currentSearchInstance->title,
        ]);
    }
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        if (!empty($this->currentCmsObject)
            && Validate::isLoadedObject($this->currentCmsObject)
            && $this->currentCmsObject->active
            && $this->currentCmsObject->isAssociatedToShop()
        ) {
            $breadcrumb['links'][] = [
                'title' => $this->currentCmsObject->meta_title,
                'url' => $this->context->link->getCMSLink($this->currentCmsObject),
            ];
        }
        $searchQuery = trim(strip_tags(Tools::getValue('as4_sq')));
        $sourceController = SearchEngineUtils::getSourceControllerFromUrl($searchQuery, $this->context->language->id);
        if ($sourceController == 'new-products') {
            $breadcrumb['links'][] = [
                'title' => $this->trans('New products', [], 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('new-products', true),
            ];
        } elseif ($sourceController == 'best-sales' && Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
            $breadcrumb['links'][] = [
                'title' => $this->trans('Best sellers', [], 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('best-sales', true),
            ];
        } elseif ($sourceController == 'prices-drop') {
            $breadcrumb['links'][] = [
                'title' => $this->trans('Prices drop', [], 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('prices-drop', true),
            ];
        }
        $breadcrumb['links'][] = [
            'title' => (!empty($this->searchInstance->title) ? $this->searchInstance->title : $this->getTranslator()->trans('Search results', [], 'Shop.Theme.Catalog')),
            'url' => $this->getCanonicalURL(),
        ];
        return $breadcrumb;
    }
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['meta']['robots'] = 'noindex';
        $page['body_classes']['as4-search-results'] = true;
        $page['body_classes']['as4-search-results-' . (int)$this->idSearch] = true;
        return $page;
    }
    protected function updateQueryString(array $extraParams = null)
    {
        if ($extraParams === null) {
            $extraParams = [];
        }
        if (array_key_exists('q', $extraParams)) {
            return parent::updateQueryString($extraParams);
        }
        return SearchEngineUtils::generateURLFromCriterions($this->getSearchEngine()->id, $this->getCriterionsList(), null, $extraParams);
    }
}
