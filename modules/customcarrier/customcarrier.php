<?php
/**
 * Custom Carrier Module for PrestaShop 8.x
 *
 * Dynamically calculates shipping costs based on product-level rules.
 *
 * @author Norwit
 * @copyright 2024 Norwit
 * @license AFL-3.0
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

// Load module classes
require_once __DIR__ . '/src/Entity/CustomCarrierProduct.php';
require_once __DIR__ . '/src/Repository/CustomCarrierProductRepository.php';

class CustomCarrier extends CarrierModule
{
    /** @var string */
    public $id_carrier;

    /** @var array */
    protected const HOOKS = [
        'displayAdminProductsShippingStepBottom',
        'actionProductUpdate',
        'actionProductAdd',
        'displayBackOfficeHeader',
        'updateCarrier',
    ];

    /** @var array */
    protected const CONFIG_KEYS = [
        'CUSTOMCARRIER_ACTIVE',
        'CUSTOMCARRIER_NAME',
        'CUSTOMCARRIER_ID_CARRIER',
        'CUSTOMCARRIER_DEFAULT_COST',
    ];

    public function __construct()
    {
        $this->name = 'customcarrier';
        $this->tab = 'shipping_logistics';
        $this->version = '1.4.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Custom Carrier', [], 'Modules.Customcarrier.Admin');
        $this->description = $this->trans(
            'Dynamic shipping cost calculation based on product-level rules.',
            [],
            'Modules.Customcarrier.Admin'
        );
        $this->confirmUninstall = $this->trans(
            'Are you sure you want to uninstall this module?',
            [],
            'Modules.Customcarrier.Admin'
        );

        // Auto-upgrade database schema for existing installations
        $this->upgradeDatabase();
    }

    /**
     * Upgrade database schema - add missing columns
     */
    protected function upgradeDatabase(): void
    {
        try {
            $db = Db::getInstance();
            $tableName = _DB_PREFIX_ . 'customcarrier_product';

            // Check if free_shipping_from_price column exists
            $columns = $db->executeS("SHOW COLUMNS FROM `{$tableName}` LIKE 'free_shipping_from_price'");
            if (empty($columns)) {
                $db->execute("ALTER TABLE `{$tableName}` ADD `free_shipping_from_price` DECIMAL(20,6) DEFAULT NULL COMMENT 'Darmowa wysyłka gdy cena produktu >= tej wartości' AFTER `cost_above_max_packages`");
            }

            // Check if max_weight_per_package column exists
            $columns = $db->executeS("SHOW COLUMNS FROM `{$tableName}` LIKE 'max_weight_per_package'");
            if (empty($columns)) {
                $db->execute("ALTER TABLE `{$tableName}` ADD COLUMN `max_weight_per_package` DECIMAL(20,6) DEFAULT NULL COMMENT 'Maksymalna waga na paczkę (kg)' AFTER `max_quantity_per_package`");
            }
        } catch (Exception $e) {
            // Silently fail - table might not exist yet during fresh install
        }
    }

    /**
     * Module installation
     */
    public function install(): bool
    {
        if (!parent::install()) {
            return false;
        }

        // Register hooks
        foreach (self::HOOKS as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        // Install database tables
        if (!$this->installDatabase()) {
            return false;
        }

        // Create carrier
        if (!$this->createCarrier()) {
            return false;
        }

        // Set default configuration
        Configuration::updateValue('CUSTOMCARRIER_ACTIVE', 1);
        Configuration::updateValue('CUSTOMCARRIER_NAME', $this->trans('Courier shipping', [], 'Modules.Customcarrier.Admin'));
        Configuration::updateValue('CUSTOMCARRIER_DEFAULT_COST', 15.00);

        // Install admin tab for bulk settings
        if (!$this->installTab()) {
            return false;
        }

        return true;
    }

    /**
     * Module uninstallation
     */
    public function uninstall(): bool
    {
        // Uninstall admin tab
        $this->uninstallTab();

        // Delete carrier
        $this->deleteCarrier();

        // Uninstall database tables
        $this->uninstallDatabase();

        // Remove configuration
        foreach (self::CONFIG_KEYS as $key) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall();
    }

    /**
     * Install admin tab for bulk settings
     */
    private function installTab(): bool
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminCustomCarrierBulk';
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Custom Carrier Bulk';
        }

        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * Uninstall admin tab
     */
    private function uninstallTab(): bool
    {
        $idTab = (int) Tab::getIdFromClassName('AdminCustomCarrierBulk');
        if ($idTab) {
            $tab = new Tab($idTab);
            return $tab->delete();
        }
        return true;
    }

    /**
     * Install database tables
     */
    protected function installDatabase(): bool
    {
        $sqlFile = dirname(__FILE__) . '/sql/install.sql';
        if (!file_exists($sqlFile)) {
            return false;
        }

        $sql = file_get_contents($sqlFile);
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);

        // Remove SQL comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // Fallback for older MySQL versions without utf8mb4 support
        $mysqlVersion = Db::getInstance()->getValue('SELECT VERSION()');
        if (version_compare($mysqlVersion, '5.5.3', '<')) {
            $sql = str_replace('utf8mb4', 'utf8', $sql);
            $sql = str_replace('utf8_unicode_ci', 'utf8_general_ci', $sql);
        }

        // Split by semicolon (handles various line ending formats)
        $queries = preg_split('/;\s*/', $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^[\s]*$/', $query)) {
                try {
                    if (!Db::getInstance()->execute($query)) {
                        PrestaShopLogger::addLog(
                            'CustomCarrier install error: ' . Db::getInstance()->getMsgError(),
                            3,
                            null,
                            'CustomCarrier'
                        );
                        return false;
                    }
                } catch (Exception $e) {
                    PrestaShopLogger::addLog(
                        'CustomCarrier install exception: ' . $e->getMessage(),
                        3,
                        null,
                        'CustomCarrier'
                    );
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Uninstall database tables
     */
    protected function uninstallDatabase(): bool
    {
        $sqlFile = dirname(__FILE__) . '/sql/uninstall.sql';
        if (!file_exists($sqlFile)) {
            return true;
        }

        $sql = file_get_contents($sqlFile);
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);

        // Remove SQL comments
        $sql = preg_replace('/--.*$/m', '', $sql);

        // Split by semicolon (handles various line ending formats)
        $queries = preg_split('/;\s*/', $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^[\s]*$/', $query)) {
                try {
                    Db::getInstance()->execute($query);
                } catch (Exception $e) {
                    // Log but continue - uninstall should not fail
                    PrestaShopLogger::addLog(
                        'CustomCarrier uninstall warning: ' . $e->getMessage(),
                        2,
                        null,
                        'CustomCarrier'
                    );
                }
            }
        }

        return true;
    }

    /**
     * Create the carrier
     */
    protected function createCarrier(): bool
    {
        $carrier = new Carrier();
        $carrier->name = Configuration::get('CUSTOMCARRIER_NAME') ?: $this->trans('Courier shipping', [], 'Modules.Customcarrier.Admin');
        $carrier->active = true;
        $carrier->deleted = false;
        $carrier->shipping_handling = false;
        $carrier->range_behavior = 0;
        $carrier->is_module = true;
        $carrier->is_free = false;
        $carrier->shipping_external = true;
        $carrier->need_range = true;
        $carrier->external_module_name = $this->name;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;

        // Set delay for all languages
        $languages = Language::getLanguages(false);
        $delay = [];
        foreach ($languages as $language) {
            $delay[$language['id_lang']] = $this->trans('1-3 business days', [], 'Modules.Customcarrier.Shop');
        }
        $carrier->delay = $delay;

        if (!$carrier->add()) {
            return false;
        }

        // Assign carrier to all groups
        $groups = Group::getGroups(true);
        foreach ($groups as $group) {
            Db::getInstance()->insert('carrier_group', [
                'id_carrier' => (int) $carrier->id,
                'id_group' => (int) $group['id_group'],
            ]);
        }

        // Create price range
        $rangePrice = new RangePrice();
        $rangePrice->id_carrier = (int) $carrier->id;
        $rangePrice->delimiter1 = 0;
        $rangePrice->delimiter2 = 10000;
        $rangePrice->add();

        // Create weight range
        $rangeWeight = new RangeWeight();
        $rangeWeight->id_carrier = (int) $carrier->id;
        $rangeWeight->delimiter1 = 0;
        $rangeWeight->delimiter2 = 10000;
        $rangeWeight->add();

        // Assign carrier to all zones
        $zones = Zone::getZones(true);
        foreach ($zones as $zone) {
            Db::getInstance()->insert('carrier_zone', [
                'id_carrier' => (int) $carrier->id,
                'id_zone' => (int) $zone['id_zone'],
            ]);

            // Create delivery entry for price range
            Db::getInstance()->insert('delivery', [
                'id_carrier' => (int) $carrier->id,
                'id_range_price' => (int) $rangePrice->id,
                'id_range_weight' => null,
                'id_zone' => (int) $zone['id_zone'],
                'price' => 0,
            ]);
        }

        // Save carrier ID in configuration
        Configuration::updateValue('CUSTOMCARRIER_ID_CARRIER', (int) $carrier->id);

        // Copy logo
        $logoSource = dirname(__FILE__) . '/logo.png';
        if (file_exists($logoSource)) {
            copy($logoSource, _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg');
        }

        return true;
    }

    /**
     * Delete the carrier
     */
    protected function deleteCarrier(): bool
    {
        $idCarrier = (int) Configuration::get('CUSTOMCARRIER_ID_CARRIER');
        if ($idCarrier) {
            $carrier = new Carrier($idCarrier);
            if (Validate::isLoadedObject($carrier)) {
                $carrier->deleted = true;
                $carrier->save();
            }
        }

        return true;
    }

    /**
     * Calculate shipping cost for cart
     *
     * @param Cart $cart The cart object
     * @param float $shippingCost Base shipping cost from PrestaShop
     * @return float|false Shipping cost or false if not available
     */
    public function getOrderShippingCost($cart, $shippingCost)
    {
        if (!Configuration::get('CUSTOMCARRIER_ACTIVE')) {
            return false;
        }

        if (!Validate::isLoadedObject($cart)) {
            return false;
        }

        // Base cost from carrier wizard (price ranges)
        $wizardCost = (float) $shippingCost;

        $products = $cart->getProducts();
        if (empty($products)) {
            // Return wizard cost for empty cart so carrier is visible
            return $wizardCost > 0 ? $wizardCost : 0.0;
        }

        // Check if ANY product has custom settings
        $hasAnyCustomSettings = false;
        foreach ($products as $product) {
            $settings = $this->getProductTransportSettings((int) $product['id_product']);
            if (!empty($settings) && $this->hasActiveCustomSettings($settings)) {
                $hasAnyCustomSettings = true;
                break;
            }
        }

        // If NO products have custom settings, just return wizard cost
        if (!$hasAnyCustomSettings) {
            return $wizardCost;
        }

        // Calculate totals for threshold products (threshold from wizard = price 0 range)
        // Module no longer uses zone thresholds - wizard handles free shipping via price ranges
        $thresholdMet = ($wizardCost == 0);

        // Shipping cost logic:
        // - separate_package = ON: sum costs (each product ships separately)
        // - multiply_by_quantity = ON: sum costs (each unit adds to shipping)
        // - Both OFF: take only the highest cost (all fit in one package)
        $summedCosts = 0.0;
        $standardPackageCosts = [];
        $hasProductsWithoutCustomSettings = false;

        foreach ($products as $product) {
            $settings = $this->getProductTransportSettings((int) $product['id_product']);

            // Product without custom settings - will use wizard cost
            if (empty($settings) || !$this->hasActiveCustomSettings($settings)) {
                $hasProductsWithoutCustomSettings = true;
                continue;
            }

            $productCost = $this->calculateProductShippingCost(
                (int) $product['id_product'],
                (int) $product['cart_quantity'],
                $thresholdMet,
                $wizardCost  // Pass wizard cost as fallback for products without base_shipping_cost
            );

            // Sum costs if: separate_package OR multiply_by_quantity is enabled
            if (!empty($settings['separate_package']) || !empty($settings['multiply_by_quantity'])) {
                $summedCosts += $productCost;
            } else {
                // Standard package - collect costs, will take highest
                $standardPackageCosts[] = $productCost;
            }
        }

        // For standard products with custom settings, take only the highest shipping cost
        $maxStandardCost = !empty($standardPackageCosts) ? max($standardPackageCosts) : 0.0;

        // Shipping cost logic:
        // - $summedCosts: products with separate_package/multiply_by_quantity (ALWAYS added)
        // - $maxStandardCost: standard products share one package (take highest)
        // - $wizardCost: products without custom settings use wizard cost

        if ($hasProductsWithoutCustomSettings) {
            // Products without settings share package with standard products → take max
            // Then ADD separate package costs on top
            $basePackageCost = max($wizardCost, $maxStandardCost);
            return $basePackageCost + $summedCosts;
        }

        // All products have custom settings
        return $summedCosts + $maxStandardCost;
    }

    /**
     * Check if product settings have any active custom configuration
     */
    protected function hasActiveCustomSettings(array $settings): bool
    {
        return !empty($settings['base_shipping_cost']) && (float)$settings['base_shipping_cost'] > 0
            || !empty($settings['free_shipping'])
            || !empty($settings['free_shipping_quantity']) && (int)$settings['free_shipping_quantity'] > 0
            || !empty($settings['free_shipping_from_price']) && (float)$settings['free_shipping_from_price'] > 0
            || !empty($settings['multiply_by_quantity'])
            || !empty($settings['separate_package'])
            || !empty($settings['exclude_from_free_shipping'])
            || !empty($settings['apply_threshold'])
            || !empty($settings['max_quantity_per_package']) && (int)$settings['max_quantity_per_package'] > 0
            || !empty($settings['max_weight_per_package']) && (float)$settings['max_weight_per_package'] > 0;
    }

    /**
     * Calculate shipping cost for external calls
     *
     * @param array $params Parameters
     * @return float|false
     */
    public function getOrderShippingCostExternal($params)
    {
        // Delegate to main method
        $cart = $params['cart'] ?? null;
        if (!$cart) {
            return false;
        }

        return $this->getOrderShippingCost($cart, 0);
    }

    /**
     * Calculate shipping cost for a single product
     *
     * @param int $idProduct Product ID
     * @param int $quantity Quantity in cart
     * @param bool $thresholdMet Whether zone threshold is met
     * @param bool $isPolandZone Whether current zone is Poland
     * @param bool $freeShippingOnlyPoland Whether free shipping is limited to Poland
     */
    protected function calculateProductShippingCost(
        int $idProduct,
        int $quantity,
        bool $thresholdMet = false,
        float $wizardCost = 0.0
    ): float {
        $settings = $this->getProductTransportSettings($idProduct);

        // No custom settings - return 0, wizard handles base cost
        if (empty($settings) || !$this->hasActiveCustomSettings($settings)) {
            return 0.0;
        }

        // Check if product is excluded from free shipping
        $excludedFromFreeShipping = !empty($settings['exclude_from_free_shipping']);

        // Free shipping - unconditional (but check exclusion)
        if (!empty($settings['free_shipping']) && !$excludedFromFreeShipping) {
            return 0.0;
        }

        // Free shipping from product price - free when product price >= threshold
        $freeShippingFromPrice = isset($settings['free_shipping_from_price']) ? (float) $settings['free_shipping_from_price'] : 0;
        if ($freeShippingFromPrice > 0 && !$excludedFromFreeShipping) {
            // Get product price (brutto)
            $productPrice = Product::getPriceStatic($idProduct, true); // true = with tax
            if ($productPrice >= $freeShippingFromPrice) {
                return 0.0;
            }
        }

        // Quantity threshold - free shipping when quantity >= threshold
        $freeShippingQuantity = (int) ($settings['free_shipping_quantity'] ?? 0);
        if ($freeShippingQuantity > 0 && $quantity >= $freeShippingQuantity && !$excludedFromFreeShipping) {
            return 0.0;
        }

        // Threshold met (wizard returned 0) - free shipping for products with apply_threshold
        // But NOT for separate_package products - they always pay
        // And NOT for excluded products
        if ($thresholdMet && !empty($settings['apply_threshold']) && empty($settings['separate_package']) && !$excludedFromFreeShipping) {
            return 0.0;
        }

        // Admin enters prices as BRUTTO - return as-is (no VAT conversion)
        // PrestaShop will display the price as entered
        $baseCost = (float) ($settings['base_shipping_cost'] ?? 0.0);

        // If no base cost set, we need a fallback
        if ($baseCost <= 0) {
            // For products excluded from free shipping or with separate_package,
            // we should NOT use wizard cost as fallback (wizard = 0 when threshold met)
            // Instead, get the lowest carrier price from database
            if ($excludedFromFreeShipping || !empty($settings['separate_package'])) {
                $baseCost = $this->getLowestCarrierPriceForProduct($idProduct);
            } else {
                $baseCost = $wizardCost;
            }
        }

        // Max quantity per package logic
        $maxQuantityPerPackage = isset($settings['max_quantity_per_package']) ? (int) $settings['max_quantity_per_package'] : 0;
        $maxWeightPerPackage = isset($settings['max_weight_per_package']) ? (float) $settings['max_weight_per_package'] : 0;
        $maxPackages = isset($settings['max_packages']) ? (int) $settings['max_packages'] : 0;
        $costAboveMaxPackages = isset($settings['cost_above_max_packages']) ? (float) $settings['cost_above_max_packages'] : null;

        // Quantity-based package calculation
        if ($maxQuantityPerPackage > 0) {
            // Calculate number of packages needed
            $packageCount = (int) ceil($quantity / $maxQuantityPerPackage);

            // If max packages limit is set and exceeded, use flat pallet cost
            // Example: max 2 pcs/package, max 2 packages at 60 PLN, but 5+ pcs (3 packages) = 140 PLN total (pallet)
            if ($maxPackages > 0 && $packageCount > $maxPackages && $costAboveMaxPackages !== null && $costAboveMaxPackages > 0) {
                // Return flat pallet cost - replaces all package costs
                return $costAboveMaxPackages;
            }

            // Otherwise multiply base cost by number of packages
            return $baseCost * $packageCount;
        }

        // Weight-based package calculation (e.g., shovels: max 30 kg per package)
        if ($maxWeightPerPackage > 0) {
            // Get product weight from PrestaShop
            $product = new Product($idProduct, false);
            $productWeight = (float) $product->weight;
            if ($productWeight > 0) {
                $totalWeight = $productWeight * $quantity;
                $packageCount = (int) ceil($totalWeight / $maxWeightPerPackage);

                // If max packages limit is set and exceeded, use flat pallet cost
                if ($maxPackages > 0 && $packageCount > $maxPackages && $costAboveMaxPackages !== null && $costAboveMaxPackages > 0) {
                    return $costAboveMaxPackages;
                }

                // Otherwise multiply base cost by number of packages
                return $baseCost * $packageCount;
            }
        }

        // Multiply by quantity if enabled
        if (!empty($settings['multiply_by_quantity'])) {
            return $baseCost * $quantity;
        }

        return $baseCost;
    }

    /**
     * Get transport settings for a product
     */
    public function getProductTransportSettings(int $idProduct): array
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('customcarrier_product');
        $sql->where('id_product = ' . (int) $idProduct);

        $result = Db::getInstance()->getRow($sql);

        return $result ?: [];
    }

    /**
     * Get the lowest carrier price for a product
     * Used as fallback for excluded/separate_package products without base_shipping_cost
     *
     * @param int $idProduct Product ID
     * @return float Price in NETTO (PrestaShop carrier prices are stored as netto)
     */
    protected function getLowestCarrierPriceForProduct(int $idProduct): float
    {
        $db = Db::getInstance();

        // Get the carrier ID configured for this module
        $carrierId = (int) Configuration::get('CUSTOMCARRIER_ID_CARRIER');

        if ($carrierId > 0) {
            // Get the base price (lowest range, price > 0) from the module's carrier
            // Note: Don't add LIMIT 1 manually - getRow() adds it automatically
            $sql = 'SELECT d.price FROM ' . _DB_PREFIX_ . 'delivery d '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'range_price rp ON d.id_range_price = rp.id_range_price '
                . 'WHERE d.id_carrier = ' . $carrierId . ' '
                . 'AND d.id_zone = 1 '
                . 'AND d.price > 0 '
                . 'ORDER BY rp.delimiter1 ASC';

            $result = $db->getRow($sql);

            if ($result && isset($result['price']) && (float) $result['price'] > 0) {
                return (float) $result['price'];
            }
        }

        // Ultimate fallback: use default cost from configuration or hardcoded value
        $defaultCost = (float) Configuration::get('CUSTOMCARRIER_DEFAULT_COST');
        if ($defaultCost > 0) {
            // Convert brutto to netto (PrestaShop adds VAT)
            return $defaultCost / 1.23;
        }

        // Hardcoded fallback: 30 zł netto (standard shipping cost)
        return 30.0;
    }

    /**
     * Save transport settings for a product
     */
    public function saveProductTransportSettings(int $idProduct, array $data): bool
    {
        $exists = $this->getProductTransportSettings($idProduct);

        $fields = [
            'id_product' => (int) $idProduct,
            'free_shipping' => !empty($data['free_shipping']) ? 1 : 0,
            'base_shipping_cost' => (float) ($data['base_shipping_cost'] ?? 0),
            'multiply_by_quantity' => !empty($data['multiply_by_quantity']) ? 1 : 0,
            'free_shipping_quantity' => (int) ($data['free_shipping_quantity'] ?? 0),
            'apply_threshold' => !empty($data['apply_threshold']) ? 1 : 0,
            'separate_package' => !empty($data['separate_package']) ? 1 : 0,
            'exclude_from_free_shipping' => !empty($data['exclude_from_free_shipping']) ? 1 : 0,
            'max_quantity_per_package' => isset($data['max_quantity_per_package']) ? (int) $data['max_quantity_per_package'] : null,
            'max_weight_per_package' => isset($data['max_weight_per_package']) && $data['max_weight_per_package'] !== '' ? (float) $data['max_weight_per_package'] : null,
            'max_packages' => isset($data['max_packages']) ? (int) $data['max_packages'] : null,
            'cost_above_max_packages' => isset($data['cost_above_max_packages']) ? (float) $data['cost_above_max_packages'] : null,
            'free_shipping_from_price' => isset($data['free_shipping_from_price']) && $data['free_shipping_from_price'] !== '' ? (float) $data['free_shipping_from_price'] : null,
            'date_upd' => date('Y-m-d H:i:s'),
        ];

        if (empty($exists)) {
            $fields['date_add'] = date('Y-m-d H:i:s');
            return Db::getInstance()->insert('customcarrier_product', $fields);
        }

        return Db::getInstance()->update(
            'customcarrier_product',
            $fields,
            'id_product = ' . (int) $idProduct
        );
    }

    /**
     * Hook: Update carrier ID when carrier is updated
     */
    public function hookUpdateCarrier(array $params): void
    {
        $idCarrierOld = (int) $params['id_carrier'];
        $idCarrierNew = (int) $params['carrier']->id;

        if ($idCarrierOld === (int) Configuration::get('CUSTOMCARRIER_ID_CARRIER')) {
            Configuration::updateValue('CUSTOMCARRIER_ID_CARRIER', $idCarrierNew);
        }
    }

    /**
     * Hook: Display transport settings on product page (Shipping tab)
     */
    public function hookDisplayAdminProductsShippingStepBottom(array $params): string
    {
        $idProduct = (int) ($params['id_product'] ?? 0);
        $settings = $this->getProductTransportSettings($idProduct);

        // Get default currency sign
        $defaultCurrency = Currency::getDefaultCurrency();
        $currencySign = $defaultCurrency ? $defaultCurrency->sign : 'zł';

        $this->context->smarty->assign([
            'customcarrier_settings' => $settings,
            'customcarrier_id_product' => $idProduct,
            'customcarrier_currency_sign' => $currencySign,
            'customcarrier_ajax_url' => _PS_MODULE_DIR_ . 'customcarrier/ajax-save.php',
            'customcarrier_ajax_url_web' => __PS_BASE_URI__ . 'modules/customcarrier/ajax-save.php',
        ]);

        return $this->display(__FILE__, 'views/templates/admin/product_tab.tpl');
    }

    /**
     * Hook: Save product transport settings after product update
     */
    public function hookActionProductUpdate(array $params): void
    {
        $this->saveProductFromPost($params);
    }

    /**
     * Hook: Save product transport settings after product add
     */
    public function hookActionProductAdd(array $params): void
    {
        $this->saveProductFromPost($params);
    }

    /**
     * Save product transport settings from POST data
     */
    protected function saveProductFromPost(array $params): void
    {
        // Get product ID from params or POST
        $idProduct = 0;

        if (isset($params['id_product'])) {
            $idProduct = (int) $params['id_product'];
        } elseif (isset($params['product']) && $params['product'] instanceof Product) {
            $idProduct = (int) $params['product']->id;
        }

        if ($idProduct <= 0) {
            return;
        }

        // Check if our fields were submitted
        if (!Tools::getIsset('customcarrier_free_shipping')) {
            return;
        }

        $this->saveProductTransportSettings($idProduct, [
            'free_shipping' => (int) Tools::getValue('customcarrier_free_shipping'),
            'base_shipping_cost' => (float) Tools::getValue('customcarrier_base_shipping_cost'),
            'multiply_by_quantity' => (int) Tools::getValue('customcarrier_multiply_by_quantity'),
            'free_shipping_quantity' => (int) Tools::getValue('customcarrier_free_shipping_quantity'),
            'free_shipping_from_price' => Tools::getValue('customcarrier_free_shipping_from_price') !== '' ? (float) Tools::getValue('customcarrier_free_shipping_from_price') : null,
            'apply_threshold' => (int) Tools::getValue('customcarrier_apply_threshold'),
            'separate_package' => (int) Tools::getValue('customcarrier_separate_package'),
            'exclude_from_free_shipping' => (int) Tools::getValue('customcarrier_exclude_from_free_shipping'),
            'max_quantity_per_package' => Tools::getValue('customcarrier_max_quantity_per_package') ? (int) Tools::getValue('customcarrier_max_quantity_per_package') : null,
            'max_packages' => Tools::getValue('customcarrier_max_packages') ? (int) Tools::getValue('customcarrier_max_packages') : null,
            'cost_above_max_packages' => Tools::getValue('customcarrier_cost_above_max_packages') ? (float) Tools::getValue('customcarrier_cost_above_max_packages') : null,
        ]);
    }

    /**
     * Hook: Load CSS/JS in back office
     */
    public function hookDisplayBackOfficeHeader(): string
    {
        $controller = Tools::getValue('controller');

        if ($controller === 'AdminProducts') {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }

        if ($controller === 'AdminCustomCarrierBulk') {
            $this->context->controller->addCSS($this->_path . 'views/css/bulk_shipping.css');
        }

        return '';
    }

    /**
     * Module configuration page
     */
    public function getContent(): string
    {
        $output = '';

        // Link to bulk settings page
        $bulkLink = $this->context->link->getAdminLink('AdminCustomCarrierBulk');
        $output .= '<div class="alert alert-info">'
            . '<i class="icon-truck"></i> '
            . $this->trans('Bulk edit shipping settings for multiple products:', [], 'Modules.Customcarrier.Admin')
            . ' <a href="' . $bulkLink . '" class="btn btn-default btn-sm">'
            . '<i class="icon-list"></i> '
            . $this->trans('Open Bulk Settings', [], 'Modules.Customcarrier.Admin')
            . '</a></div>';

        // Handle main form submission - only enable/disable toggle
        if (Tools::isSubmit('submitCustomCarrierConfig')) {
            $active = (int) Tools::getValue('CUSTOMCARRIER_ACTIVE');
            Configuration::updateValue('CUSTOMCARRIER_ACTIVE', $active);

            // Update carrier active state
            $idCarrier = (int) Configuration::get('CUSTOMCARRIER_ID_CARRIER');
            if ($idCarrier) {
                $carrier = new Carrier($idCarrier);
                if (Validate::isLoadedObject($carrier)) {
                    $carrier->active = (bool) $active;
                    $carrier->save();
                }
            }

            $output .= $this->displayConfirmation($this->trans('Settings saved successfully.', [], 'Modules.Customcarrier.Admin'));
        }

        return $output . $this->renderConfigForm();
    }

    /**
     * Render configuration form
     * Simplified: only enable/disable toggle. Base shipping costs come from carrier wizard.
     */
    protected function renderConfigForm(): string
    {
        $fields = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Customcarrier.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'description' => $this->trans('Base shipping costs are configured in the carrier wizard (Shipping > Carriers > Edit). This module adds per-product shipping settings.', [], 'Modules.Customcarrier.Admin'),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable module', [], 'Modules.Customcarrier.Admin'),
                        'name' => 'CUSTOMCARRIER_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->trans('Enable or disable custom shipping calculations for products.', [], 'Modules.Customcarrier.Admin'),
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Admin.Global')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Admin.Global')],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitCustomCarrierConfig';

        $helper->fields_value = [
            'CUSTOMCARRIER_ACTIVE' => Configuration::get('CUSTOMCARRIER_ACTIVE'),
        ];

        return $helper->generateForm([$fields]);
    }
}
