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

namespace AdvancedSearch;
if (!defined('_PS_VERSION_')) {
    exit;
}
use Hook;
use Tools;
use Module;
use Language;
use Validate;
use Exception;
use Configuration;
use PM_AdvancedSearch4;
use ProductListingFrontController;
use AdvancedSearch\SearchProvider\Facets;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;
abstract class AdvancedSearchProductListingFrontController extends ProductListingFrontController
{
    protected $module;
    protected $idSearch;
    protected $idSeo;
    protected $searchInstance;
    protected $currentCategoryObject;
    protected $currentManufacturerObject;
    protected $currentSupplierObject;
    protected $currentCmsObject;
    protected $criterionsList = [];
    protected $criterionsListHidden = [];
    public function init()
    {
        if (empty($this->module) || !is_object($this->module)) {
            $moduleInstance = Module::getInstanceByName(_PM_AS_MODULE_NAME_);
            $this->module = $moduleInstance;
        }
        parent::init();
    }
    public function getListingLabel()
    {
        return $this->getTranslator()->trans('Search results', [], 'Shop.Theme.Catalog');
    }
    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        return $query;
    }
    protected function getDefaultProductSearchProvider()
    {
        return new Facets(
            $this->module,
            $this->getTranslator(),
            $this->getSearchEngine(),
            $this->getCriterionsList()
        );
    }
    private function getProductSearchProviderFromModules($query)
    {
        return null;
    }
    protected function getProductSearchVariables()
    {
        $context = $this->getProductSearchContext();
        $query = $this->getProductSearchQuery();
        $provider = $this->getProductSearchProviderFromModules($query);
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }
        $resultsPerPage = (int)Tools::getValue('resultsPerPage');
        if ($resultsPerPage <= 0 || $resultsPerPage > 36) {
            $resultsPerPage = (int)Configuration::get('PS_PRODUCTS_PER_PAGE');
        }
        $query
            ->setResultsPerPage($resultsPerPage)
            ->setPage(max((int)Tools::getValue('page'), 1))
        ;
        if (Tools::getValue('order')) {
            $encodedSortOrder = Tools::getValue('order');
        } else {
            $encodedSortOrder = Tools::getValue('orderby', null);
        }
        if ($encodedSortOrder) {
            try {
                $selectedSortOrder = SortOrder::newFromString($encodedSortOrder);
            } catch (Exception $e) {
                $defaultSearchEngineOrderBy = SearchEngineUtils::getOrderByValue($this->getSearchEngine());
                $defaultSearchEngineOrderWay = SearchEngineUtils::getOrderWayValue($this->getSearchEngine());
                $selectedSortOrder = new SortOrder('product', $defaultSearchEngineOrderBy, $defaultSearchEngineOrderWay);
            }
            $query->setSortOrder($selectedSortOrder);
        }
        $encodedFacets = Tools::getValue('q');
        $query->setEncodedFacets($encodedFacets);
        $result = $provider->runQuery(
            $context,
            $query
        );
        if (!$result->getCurrentSortOrder()) {
            $result->setCurrentSortOrder($query->getSortOrder());
        }
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );
        if ($provider instanceof FacetsRendererInterface) {
            $rendered_facets = $provider->renderFacets(
                $context,
                $result
            );
            $rendered_active_filters = $provider->renderActiveFilters(
                $context,
                $result
            );
        } else {
            $rendered_facets = $this->renderFacets(
                $result
            );
            $rendered_active_filters = $this->renderActiveFilters(
                $result
            );
        }
        $pagination = $this->getTemplateVarPagination(
            $query,
            $result
        );
        $sort_orders = $this->getTemplateVarSortOrders(
            $result->getAvailableSortOrders(),
            $query->getSortOrder()->toString()
        );
        $sort_selected = false;
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if (array_key_exists('current', $order) && isset($order['current']) && (true === $order['current'])) {
                    $sort_selected = $order['label'];
                    break;
                }
            }
        }
        $currentUrlParams = [
            'q' => $result->getEncodedFacets(),
        ];
        if ((Tools::getIsset('order') || Tools::getIsset('orderby')) && $result->getCurrentSortOrder() != null) {
            $currentUrlParams['order'] = $result->getCurrentSortOrder()->toString();
        }
        $searchVariables = [
            'result' => $result,
            'label' => $this->getListingLabel(),
            'products' => $products,
            'sort_orders' => $sort_orders,
            'sort_selected' => $sort_selected,
            'pagination' => $pagination,
            'rendered_facets' => $rendered_facets,
            'rendered_active_filters' => $rendered_active_filters,
            'js_enabled' => $this->ajax,
            'current_url' => $this->updateQueryString($currentUrlParams),
        ];
        if (Tools::getValue('with_product') && $this->getSearchEngine()->search_method == 4) {
            $searchVariables['redirect_to_url'] = $searchVariables['current_url'];
        }
        Hook::exec('actionProductSearchComplete', $searchVariables);
        Hook::exec('filterProductSearch', [
            'searchVariables' => &$searchVariables,
        ]);
        Hook::exec('actionProductSearchAfter', $searchVariables);
        return $searchVariables;
    }
    protected function getTemplateVarPagination(
        ProductSearchQuery $query,
        ProductSearchResult $result
    ) {
        $pagination = parent::getTemplateVarPagination($query, $result);
        foreach ($pagination['pages'] as &$p) {
            $p['url'] = $this->updateQueryString([
                'page' => $p['page'],
                'order' => (Tools::getIsset('order') ? $query->getSortOrder()->toString() : null),
                'from_as4' => $this->getSearchEngine()->id,
            ]);
        }
        return $pagination;
    }
    protected function getTemplateVarSortOrders(array $sortOrders, $currentSortOrderURLParameter)
    {
        $sortOrders = parent::getTemplateVarSortOrders($sortOrders, $currentSortOrderURLParameter);
        foreach ($sortOrders as &$order) {
            $order['url'] = $this->updateQueryString([
                'order' => $order['urlParameter'],
                'page' => null,
                'from_as4' => $this->getSearchEngine()->id,
            ]);
        }
        return $sortOrders;
    }
    public function getTemplateVarUrls()
    {
        $urls = parent::getTemplateVarUrls();
        $urls['alternative_langs'] = $this->getAlternativeLangsUrl();
        return $urls;
    }
    protected function getAlternativeLangsUrl()
    {
        $alternativeLangs = [];
        $languages = Language::getLanguages(true, $this->context->shop->id);
        if (
            !is_array($languages)
            || is_array($languages) && count($languages) < 2
            || $this->getSearchEngine() === null
        ) {
            return $alternativeLangs;
        }
        $idSearch = $this->getSearchEngine()->id;
        $criterionsList = $this->getCriterionsList();
        foreach ($languages as $lang) {
            $alternativeLangs[$lang['language_code']] = SearchEngineUtils::generateURLFromCriterions($idSearch, $criterionsList, (int)$lang['id_lang']);
        }
        return $alternativeLangs;
    }
    protected function getAjaxProductSearchVariables()
    {
        $data = parent::getAjaxProductSearchVariables();
        $data['id_search'] = null;
        $data['remind_selection'] = null;
        if (method_exists($this, 'getIdSeo')) {
            $data['id_seo'] = (int)$this->getIdSeo();
        }
        $searchEngine = $this->getSearchEngine();
        $lastCriterionStepSelected = SearchEngineUtils::lastCriterionStepSelected($searchEngine, $this->getCriterionsList());
        if ($searchEngine->search_method == 3) {
            $data['last_criterion_selected'] = $lastCriterionStepSelected;
        }
        $withProducts = (!Tools::getIsset('with_product') || Tools::getValue('with_product')) || $lastCriterionStepSelected;
        if (!$withProducts) {
            $data['rendered_products_top'] = null;
            $data['rendered_products'] = null;
            $data['rendered_products_bottom'] = null;
        } else {
            if (!empty($searchEngine->redirect_one_product) && ($searchEngine->search_method == 2 || $searchEngine->search_method == 4) && !empty($data['products']) && is_array($data['products']) && count($data['products']) == 1) {
                $product = current($data['products']);
                if (!empty($product['url'])) {
                    $data['redirect_to_url'] = $product['url'];
                } elseif (!empty($product['link'])) {
                    $data['redirect_to_url'] = $product['link'];
                }
            }
            if ($searchEngine->search_method == 3 && !$lastCriterionStepSelected) {
                $data['rendered_products_top'] = null;
                $data['rendered_products'] = null;
                $data['rendered_products_bottom'] = null;
            }
        }
        if (Validate::isLoadedObject($searchEngine)) {
            $data['id_search'] = $searchEngine->id;
            $data['remind_selection'] = (int)$searchEngine->remind_selection;
        }
        return $data;
    }
    protected function renderFacets(ProductSearchResult $result)
    {
        $this->assignGeneralPurposeVariables();
        $this->module->setSmartyVarsForTpl($this->getSearchEngine(), $this->getCriterionsList());
        return $this->module->display(_PM_AS_MODULE_NAME_ . '.php', 'views/templates/hook/' . $this->module->getPrestaShopTemplateVersion() . '/pm_advancedsearch.tpl');
    }
    protected function renderActiveFilters(ProductSearchResult $result)
    {
        if (!in_array($this->getSearchEngine()->remind_selection, [1, 3])) {
            return '';
        }
        $this->assignGeneralPurposeVariables();
        $this->module->setSmartyVarsForTpl($this->getSearchEngine(), $this->getCriterionsList());
        return $this->module->display(_PM_AS_MODULE_NAME_ . '.php', 'views/templates/hook/' . $this->module->getPrestaShopTemplateVersion() . '/pm_advancedsearch_selection_block.tpl');
    }
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        if (!empty($this->currentCategoryObject) && Validate::isLoadedObject($this->currentCategoryObject)) {
            foreach ($this->currentCategoryObject->getAllParents() as $category) {
                if ($category->id_parent != 0 && !$category->is_root_category && $category->active) {
                    $breadcrumb['links'][] = [
                        'title' => $category->name,
                        'url' => $this->context->link->getCategoryLink($category),
                    ];
                }
            }
            if (
                $this->currentCategoryObject->id_parent != 0
                && !$this->currentCategoryObject->is_root_category
                && $this->currentCategoryObject->active
            ) {
                $breadcrumb['links'][] = [
                    'title' => $this->currentCategoryObject->name,
                    'url' => $this->context->link->getCategoryLink($this->currentCategoryObject),
                ];
            }
        }
        if (
            !empty($this->currentManufacturerObject)
            && Validate::isLoadedObject($this->currentManufacturerObject)
            && $this->currentManufacturerObject->active
            && $this->currentManufacturerObject->isAssociatedToShop()
        ) {
            $breadcrumb['links'][] = [
                'title' => $this->currentManufacturerObject->name,
                'url' => $this->context->link->getManufacturerLink($this->currentManufacturerObject),
            ];
        }
        if (
            !empty($this->currentSupplierObject)
            && Validate::isLoadedObject($this->currentSupplierObject)
            && $this->currentSupplierObject->active
            && $this->currentSupplierObject->isAssociatedToShop()
        ) {
            $breadcrumb['links'][] = [
                'title' => $this->currentSupplierObject->name,
                'url' => $this->context->link->getSupplierLink($this->currentSupplierObject),
            ];
        }
        return $breadcrumb;
    }
    public function getSearchEngine()
    {
        return $this->searchInstance;
    }
    public function getCriterionsList()
    {
        return $this->getSelectedCriterions();
    }
    public function getHiddenCriterionsList()
    {
        return $this->criterionsListHidden;
    }
    public function getSelectedCriterions()
    {
        return $this->criterionsList;
    }
    public function getCanonicalURL()
    {
        return SearchEngineUtils::generateURLFromCriterions($this->getSearchEngine()->id, $this->getSelectedCriterions());
    }
    protected function setProductFilterList()
    {
        $productFilterListSource = Tools::getValue('productFilterListSource');
        if (in_array($productFilterListSource, SearchEngineUtils::$validPageName)) {
            SearchEngineUtils::$productFilterListSource = $productFilterListSource;
            if ($productFilterListSource == 'search' || $productFilterListSource == 'jolisearch' || $productFilterListSource == 'module-ambjolisearch-jolisearch' || $productFilterListSource == 'prestasearch') {
                $productFilterListData = Core::getDataUnserialized(Tools::getValue('productFilterListData'));
                if ($productFilterListData !== false) {
                    SearchEngineUtils::$productFilterListData = $productFilterListData;
                }
            }
            $this->module->setProductFilterContext();
        }
    }
    protected function updateQueryString(array $extraParams = null)
    {
        if ($extraParams === null) {
            $extraParams = [];
        }
        return SearchEngineUtils::generateURLFromCriterions($this->getSearchEngine()->id, $this->getCriterionsList(), null, $extraParams);
    }
}
