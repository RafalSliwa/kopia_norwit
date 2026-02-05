<?php
/**
 * Norwit Delivery - Bulk edit controller
 */

declare(strict_types=1);

class AdminNorwitDeliveryController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
    }

    public function initContent(): void
    {
        parent::initContent();

        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;

        // Handle form submission
        if (Tools::isSubmit('submitBulkUpdate')) {
            $this->processBulkUpdate($idLang, $idShop);
        }

        // Get filter values
        $filterCategory = (int) Tools::getValue('filter_category', 0);
        $filterManufacturer = (int) Tools::getValue('filter_manufacturer', 0);
        $filterOutOfStock = (int) Tools::getValue('filter_out_of_stock', 0);
        $filterSearch = Tools::getValue('filter_search', '');

        // Get categories for filter
        $categories = Category::getCategories($idLang, true, false);

        // Get manufacturers for filter
        $manufacturers = Manufacturer::getManufacturers(false, $idLang);

        // Get products
        $products = $this->getFilteredProducts($idLang, $idShop, $filterCategory, $filterManufacturer, $filterOutOfStock, $filterSearch);

        $this->context->smarty->assign([
            'products' => $products,
            'categories' => $categories,
            'manufacturers' => $manufacturers,
            'filter_category' => $filterCategory,
            'filter_manufacturer' => $filterManufacturer,
            'filter_out_of_stock' => $filterOutOfStock,
            'filter_search' => $filterSearch,
            'token' => $this->token,
            'link' => $this->context->link,
            'current_url' => $this->context->link->getAdminLink('AdminNorwitDelivery'),
        ]);

        $this->setTemplate('bulk_edit.tpl');
    }

    private function getFilteredProducts(int $idLang, int $idShop, int $categoryId, int $manufacturerId, int $outOfStock, string $search): array
    {
        $sql = 'SELECT p.id_product, p.reference, pl.name, pl.available_later, sa.quantity
                FROM ' . _DB_PREFIX_ . 'product p
                JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product AND pl.id_lang = ' . $idLang . ' AND pl.id_shop = ' . $idShop . '
                JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product AND ps.id_shop = ' . $idShop . '
                LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa ON p.id_product = sa.id_product AND sa.id_product_attribute = 0 AND sa.id_shop = ' . $idShop . '
                WHERE ps.active = 1';

        if ($categoryId > 0) {
            $sql .= ' AND p.id_product IN (
                SELECT cp.id_product FROM ' . _DB_PREFIX_ . 'category_product cp WHERE cp.id_category = ' . $categoryId . '
            )';
        }

        if ($manufacturerId > 0) {
            $sql .= ' AND p.id_manufacturer = ' . $manufacturerId;
        }

        if ($outOfStock) {
            $sql .= ' AND (sa.quantity IS NULL OR sa.quantity <= 0)';
        }

        if (!empty($search)) {
            $searchSafe = pSQL($search);
            $sql .= ' AND (pl.name LIKE "%' . $searchSafe . '%" OR p.reference LIKE "%' . $searchSafe . '%")';
        }

        $sql .= ' ORDER BY pl.name ASC LIMIT 500';

        return Db::getInstance()->executeS($sql) ?: [];
    }

    private function processBulkUpdate(int $idLang, int $idShop): void
    {
        $productIds = Tools::getValue('product_ids', []);
        $availableLaterValues = Tools::getValue('available_later', []);
        $bulkText = Tools::getValue('bulk_available_later', '');
        $applyBulk = Tools::getValue('apply_bulk', 0);

        $updated = 0;

        if (!empty($productIds) && is_array($productIds)) {
            foreach ($productIds as $idProduct) {
                $idProduct = (int) $idProduct;
                if ($idProduct <= 0) {
                    continue;
                }

                // Use bulk text if apply_bulk is checked, otherwise use individual value
                $newValue = $applyBulk ? $bulkText : ($availableLaterValues[$idProduct] ?? '');

                // Update product_lang
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'product_lang
                        SET available_later = "' . pSQL($newValue) . '"
                        WHERE id_product = ' . $idProduct . '
                        AND id_lang = ' . $idLang . '
                        AND id_shop = ' . $idShop;

                if (Db::getInstance()->execute($sql)) {
                    $updated++;
                }
            }
        }

        if ($updated > 0) {
            $this->confirmations[] = sprintf('Zaktualizowano %d produktów.', $updated);
        }
    }
}
