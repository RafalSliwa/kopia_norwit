<?php
/**
 * Custom Carrier - Bulk Shipping Settings Controller
 *
 * Allows mass editing of product shipping settings
 * with filtering by manufacturer and category.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCustomCarrierBulkController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
    }

    public function postProcess()
    {
        // Handle bulk update submission
        if (Tools::isSubmit('submitBulkShipping')) {
            $this->processBulkShippingUpdate();
        }

        parent::postProcess();
    }

    public function initContent(): void
    {
        parent::initContent();

        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;

        // Filter values
        $filterCategory = (int) Tools::getValue('filter_category', 0);
        $filterManufacturer = (int) Tools::getValue('filter_manufacturer', 0);
        $filterSearch = Tools::getValue('filter_search', '');
        $filterHasSettings = (int) Tools::getValue('filter_has_settings', -1);
        $page = max(1, (int) Tools::getValue('page', 1));
        $perPage = (int) Tools::getValue('per_page', 50);
        // Validate per_page value
        $allowedPerPage = [0, 50, 100, 200, 500, 1000]; // 0 = show all
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 50;
        }

        // Data for filter dropdowns
        $categories = $this->getCategoriesFlat($idLang, $idShop);
        $manufacturers = Manufacturer::getManufacturers(false, $idLang);

        // Get total count first (needed for "show all" option)
        $totalProducts = $this->getFilteredProductsCount(
            $idLang, $idShop, $filterCategory, $filterManufacturer,
            $filterSearch, $filterHasSettings
        );

        // Handle "show all" option
        $actualPerPage = $perPage;
        if ($perPage == 0) {
            $actualPerPage = max(1, $totalProducts); // Show all products on one page
            $page = 1; // Reset to first page
        }

        // Filtered products
        $products = $this->getFilteredProducts(
            $idLang, $idShop, $filterCategory, $filterManufacturer,
            $filterSearch, $filterHasSettings, $page, $actualPerPage
        );
        $totalPages = max(1, (int) ceil($totalProducts / $actualPerPage));

        // Currency
        $defaultCurrency = Currency::getDefaultCurrency();
        $currencySign = $defaultCurrency ? $defaultCurrency->sign : 'zł';

        $this->context->smarty->assign([
            'products' => $products,
            'categories' => $categories,
            'manufacturers' => $manufacturers,
            'filter_category' => $filterCategory,
            'filter_manufacturer' => $filterManufacturer,
            'filter_search' => $filterSearch,
            'filter_has_settings' => $filterHasSettings,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_products' => $totalProducts,
            'per_page' => $perPage,
            'currency_sign' => $currencySign,
            'bulk_token' => $this->token,
            'current_url' => $this->context->link->getAdminLink('AdminCustomCarrierBulk'),
            'success_messages' => $this->confirmations,
            'error_messages' => $this->errors,
        ]);

        $this->setTemplate('bulk_shipping.tpl');
    }

    /**
     * Get flat list of categories for dropdown
     */
    private function getCategoriesFlat(int $idLang, int $idShop): array
    {
        $sql = 'SELECT c.id_category, cl.name, c.level_depth
                FROM ' . _DB_PREFIX_ . 'category c
                INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl
                    ON c.id_category = cl.id_category
                    AND cl.id_lang = ' . (int) $idLang . '
                    AND cl.id_shop = ' . (int) $idShop . '
                INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs
                    ON c.id_category = cs.id_category
                    AND cs.id_shop = ' . (int) $idShop . '
                WHERE c.active = 1 AND c.id_category > 1
                ORDER BY c.nleft ASC';

        $categories = Db::getInstance()->executeS($sql);
        if (!$categories) {
            return [];
        }

        return $categories;
    }

    /**
     * Build WHERE clause for filtered product queries
     */
    private function buildFilterWhere(
        int $idLang,
        int $idShop,
        int $categoryId,
        int $manufacturerId,
        string $search,
        int $hasSettings
    ): string {
        $where = ' WHERE ps.active = 1';

        if ($categoryId > 0) {
            $where .= ' AND p.id_product IN (
                SELECT cp2.id_product FROM ' . _DB_PREFIX_ . 'category_product cp2
                WHERE cp2.id_category = ' . (int) $categoryId . '
            )';
        }

        if ($manufacturerId > 0) {
            $where .= ' AND p.id_manufacturer = ' . (int) $manufacturerId;
        }

        if (!empty($search)) {
            $searchSafe = pSQL($search);
            $where .= ' AND (pl.name LIKE \'%' . $searchSafe . '%\'
                      OR p.reference LIKE \'%' . $searchSafe . '%\')';
        }

        if ($hasSettings === 1) {
            $where .= ' AND ccp.id_customcarrier_product IS NOT NULL';
        } elseif ($hasSettings === 0) {
            $where .= ' AND ccp.id_customcarrier_product IS NULL';
        }

        return $where;
    }

    /**
     * Build FROM/JOIN clause
     */
    private function buildFromJoin(int $idLang, int $idShop): string
    {
        return ' FROM ' . _DB_PREFIX_ . 'product p
                INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl
                    ON p.id_product = pl.id_product
                    AND pl.id_lang = ' . (int) $idLang . '
                    AND pl.id_shop = ' . (int) $idShop . '
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps
                    ON p.id_product = ps.id_product
                    AND ps.id_shop = ' . (int) $idShop . '
                LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m
                    ON p.id_manufacturer = m.id_manufacturer
                LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl
                    ON p.id_category_default = cl.id_category
                    AND cl.id_lang = ' . (int) $idLang . '
                    AND cl.id_shop = ' . (int) $idShop . '
                LEFT JOIN ' . _DB_PREFIX_ . 'customcarrier_product ccp
                    ON p.id_product = ccp.id_product';
    }

    /**
     * Get filtered products with shipping settings
     */
    private function getFilteredProducts(
        int $idLang,
        int $idShop,
        int $categoryId,
        int $manufacturerId,
        string $search,
        int $hasSettings,
        int $page,
        int $perPage
    ): array {
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT p.id_product, p.reference, pl.name AS product_name,
                       m.name AS manufacturer_name, cl.name AS category_name,
                       ps.price AS product_price,
                       ccp.free_shipping, ccp.base_shipping_cost,
                       ccp.multiply_by_quantity, ccp.free_shipping_quantity,
                       ccp.free_shipping_from_price,
                       ccp.apply_threshold, ccp.separate_package,
                       ccp.exclude_from_free_shipping,
                       ccp.max_quantity_per_package, ccp.max_weight_per_package,
                       ccp.max_packages, ccp.cost_above_max_packages,
                       IF(ccp.id_customcarrier_product IS NOT NULL, 1, 0) AS has_settings'
            . $this->buildFromJoin($idLang, $idShop)
            . $this->buildFilterWhere($idLang, $idShop, $categoryId, $manufacturerId, $search, $hasSettings)
            . ' ORDER BY pl.name ASC'
            . ' LIMIT ' . (int) $offset . ', ' . (int) $perPage;

        $products = Db::getInstance()->executeS($sql) ?: [];

        // Add gross price for each product
        foreach ($products as &$product) {
            $product['product_price_gross'] = Product::getPriceStatic(
                (int) $product['id_product'],
                true,  // with tax (brutto)
                null,  // no specific attribute
                2      // 2 decimal places
            );
        }

        return $products;
    }

    /**
     * Count filtered products for pagination
     */
    private function getFilteredProductsCount(
        int $idLang,
        int $idShop,
        int $categoryId,
        int $manufacturerId,
        string $search,
        int $hasSettings
    ): int {
        $sql = 'SELECT COUNT(DISTINCT p.id_product) AS total'
            . $this->buildFromJoin($idLang, $idShop)
            . $this->buildFilterWhere($idLang, $idShop, $categoryId, $manufacturerId, $search, $hasSettings);

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Process bulk shipping update
     */
    private function processBulkShippingUpdate(): void
    {
        $productIds = Tools::getValue('product_ids', []);

        if (empty($productIds) || !is_array($productIds)) {
            $this->errors[] = $this->module->l('No products selected.', 'AdminCustomCarrierBulkController');
            return;
        }

        $fieldsToApply = Tools::getValue('bulk_fields', []);
        if (empty($fieldsToApply) || !is_array($fieldsToApply)) {
            $this->errors[] = $this->module->l('No shipping fields selected to apply.', 'AdminCustomCarrierBulkController');
            return;
        }

        // Read bulk values
        $bulkData = [
            'free_shipping' => (int) Tools::getValue('bulk_free_shipping', 0),
            'base_shipping_cost' => (float) Tools::getValue('bulk_base_shipping_cost', 0),
            'multiply_by_quantity' => (int) Tools::getValue('bulk_multiply_by_quantity', 0),
            'free_shipping_quantity' => (int) Tools::getValue('bulk_free_shipping_quantity', 0),
            'apply_threshold' => (int) Tools::getValue('bulk_apply_threshold', 0),
            'separate_package' => (int) Tools::getValue('bulk_separate_package', 0),
            'exclude_from_free_shipping' => (int) Tools::getValue('bulk_exclude_from_free_shipping', 0),
            'max_quantity_per_package' => Tools::getValue('bulk_max_quantity_per_package') !== ''
                ? (int) Tools::getValue('bulk_max_quantity_per_package') : null,
            'max_weight_per_package' => Tools::getValue('bulk_max_weight_per_package') !== ''
                ? (float) Tools::getValue('bulk_max_weight_per_package') : null,
            'max_packages' => Tools::getValue('bulk_max_packages') !== ''
                ? (int) Tools::getValue('bulk_max_packages') : null,
            'cost_above_max_packages' => Tools::getValue('bulk_cost_above_max_packages') !== ''
                ? (float) Tools::getValue('bulk_cost_above_max_packages') : null,
            'free_shipping_from_price' => Tools::getValue('bulk_free_shipping_from_price') !== ''
                ? (float) Tools::getValue('bulk_free_shipping_from_price') : null,
        ];

        $allFields = [
            'free_shipping', 'base_shipping_cost', 'multiply_by_quantity',
            'free_shipping_quantity', 'free_shipping_from_price', 'apply_threshold', 'separate_package',
            'exclude_from_free_shipping', 'max_quantity_per_package', 'max_weight_per_package',
            'max_packages', 'cost_above_max_packages',
        ];

        $updated = 0;

        foreach ($productIds as $idProduct) {
            $idProduct = (int) $idProduct;
            if ($idProduct <= 0) {
                continue;
            }

            // Get existing settings to merge
            $existing = $this->module->getProductTransportSettings($idProduct);

            $mergedData = [];
            foreach ($allFields as $field) {
                if (in_array($field, $fieldsToApply, true)) {
                    $mergedData[$field] = $bulkData[$field];
                } else {
                    $mergedData[$field] = $existing[$field] ?? null;
                }
            }

            if ($this->module->saveProductTransportSettings($idProduct, $mergedData)) {
                $updated++;
            }
        }

        $this->confirmations[] = sprintf(
            $this->module->l('Updated shipping settings for %d products.', 'AdminCustomCarrierBulkController'),
            $updated
        );
    }
}
