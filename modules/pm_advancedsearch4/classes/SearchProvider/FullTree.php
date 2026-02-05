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

namespace AdvancedSearch\SearchProvider;
if (!defined('_PS_VERSION_')) {
    exit;
}
use Tools;
use Module;
use Context;
use PM_AdvancedSearch4;
use AdvancedSearch\Models\Search;
use AdvancedSearch\SearchEngineUtils;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
class FullTree implements ProductSearchProviderInterface, FacetsRendererInterface
{
    private $module;
    private $translator;
    public function __construct(PM_AdvancedSearch4 $module, $translator)
    {
        $this->module = $module;
        $this->translator = $translator;
    }
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $result = new ProductSearchResult();
        $continue = true;
        $realContext = Context::getContext();
        $controller = $realContext->controller;
        if (SearchEngineUtils::isSPAModuleActive() && SearchEngineUtils::isSPAAllowedOnCategory((int)$controller->getCategory()->id_category)) {
            $pm_productsbyattributes = Module::getInstanceByName('pm_productsbyattributes');
            if (version_compare($pm_productsbyattributes->version, '1.0.4', '>=')) {
                $continue = false;
                $sortOrder = $this->getSearchEngineSortOrder($query);
                if (!empty($sortOrder)) {
                    $query->setSortOrder($sortOrder);
                }
                $sortOrders = $this->getAllSortOrders();
                if (!empty($sortOrders)) {
                    $result->setAvailableSortOrders(
                        $sortOrders
                    );
                }
                $productCount = $pm_productsbyattributes->getCategoryProducts((int)$controller->getCategory()->id_category, null, null, null, $query->getSortOrder()->toLegacyOrderBy(), $query->getSortOrder()->toLegacyOrderWay(), true, true);
                $productList = $pm_productsbyattributes->getCategoryProducts((int)$controller->getCategory()->id_category, (int)$context->getIdLang(), (int)$query->getPage(), (int)$query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(), $query->getSortOrder()->toLegacyOrderWay(), false, true);
                $result->setTotalProductsCount($productCount);
                $result->setProducts($productList);
                $pm_productsbyattributes->splitProductsList($productList);
            }
        }
        if ($continue) {
            $sortOrder = $this->getSearchEngineSortOrder($query);
            if (!empty($sortOrder)) {
                $query->setSortOrder($sortOrder);
            }
            $sortOrders = $this->getAllSortOrders();
            if (!empty($sortOrders)) {
                $result->setAvailableSortOrders(
                    $sortOrders
                );
            }
            $result->setTotalProductsCount($this->module->getCategoryProducts(null, null, null, $query->getSortOrder()->toLegacyOrderBy(), $query->getSortOrder()->toLegacyOrderWay(), true));
            $result->setProducts($this->module->getCategoryProducts((int)$context->getIdLang(), (int)$query->getPage(), (int)$query->getResultsPerPage(), $query->getSortOrder()->toLegacyOrderBy(), $query->getSortOrder()->toLegacyOrderWay()));
        }
        return $result;
    }
    protected function getSearchEngineToUse()
    {
        $enginesForHook = null;
        $hooksToCheck = array_merge(SearchEngineUtils::$valid_hooks_category, [-1]);
        foreach ($hooksToCheck as $pageHook) {
            $enginesForHook = SearchEngineUtils::getSearchsFromHook($pageHook);
            if (!empty($enginesForHook)) {
                break;
            }
        }
        if (empty($enginesForHook) || empty($enginesForHook[0]['id_search'])) {
            return null;
        }
        return new Search((int)$enginesForHook[0]['id_search']);
    }
    protected function getAllSortOrders()
    {
        $searchProvider = new Facets(
            $this->module,
            $this->translator,
            null,
            null
        );
        return $searchProvider->getSortOrders();
    }
    public function getSearchEngineSortOrder(ProductSearchQuery $query)
    {
        $currentSearchEngine = $this->getSearchEngineToUse();
        if (empty($currentSearchEngine)) {
            return null;
        }
        $engineSortOrder = null;
        if ((Tools::getIsset('order') || Tools::getIsset('orderby')) && $query->getSortOrder() != null) {
            $defaultSortOrder = SearchEngineUtils::getOrderByValue($currentSearchEngine, $query);
            $defaultSortWay = SearchEngineUtils::getOrderWayValue($currentSearchEngine, $query);
        } else {
            $defaultSortOrder = SearchEngineUtils::getOrderByValue($currentSearchEngine);
            $defaultSortWay = SearchEngineUtils::getOrderWayValue($currentSearchEngine);
        }
        $sortOrders = $this->getAllSortOrders();
        foreach ($sortOrders as $sortOrder) {
            if ($sortOrder->getField() == $defaultSortOrder && $sortOrder->getDirection() == $defaultSortWay) {
                $engineSortOrder = $sortOrder;
                break;
            }
        }
        return $engineSortOrder;
    }
    public function renderFacets(
        ProductSearchContext $context,
        ProductSearchResult $result
    ) {
        $currentSearchEngine = $this->getSearchEngineToUse();
        if (empty($currentSearchEngine)) {
            return '';
        }
        if (isset($this->module::$renderedFacetsCache[$currentSearchEngine->id])) {
            return $this->module::$renderedFacetsCache[$currentSearchEngine->id];
        }
        $fromWidget = SearchEngineUtils::getHookName($currentSearchEngine->id_hook) == 'displayAdvancedSearch4';
        $this->module::$renderedFacetsCache[$currentSearchEngine->id] = $this->module->displaySearchBlock(SearchEngineUtils::getHookName($currentSearchEngine->id_hook), 'pm_advancedsearch.tpl', [], (int)$currentSearchEngine->id, $fromWidget);
        return $this->module::$renderedFacetsCache[$currentSearchEngine->id];
    }
    public function renderActiveFilters(
        ProductSearchContext $context,
        ProductSearchResult $result
    ) {
        return '';
    }
}
