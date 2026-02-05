<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class RelatedProducts extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'relatedproducts';
        $this->version = '1.0.7';
        $this->author = 'Norwit';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Related products in modal template.');
        $this->description = $this->l('Displays advanced cart modal with delivery info and related products.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayProductModal')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayCartModalContent');
    }


    /**
     * Adds CSS and AJAX to page
     */
    public function hookDisplayHeader()
    {
        // Add free shipping threshold to Smarty global variables
        if (isset($this->context->cart) && $this->context->cart->id) {
            $products = $this->context->cart->getProducts();
            
            // Check number of products in cart
            $totalQuantity = $this->context->cart->nbProducts(); // total quantity
            $uniqueCount = count($products); // unique items
            
            // Get shipping costs
            $shippingCost = $this->context->cart->getTotalShippingCost();
            $shippingCostWithTax = $this->context->cart->getTotalShippingCost(null, true);
            $shippingCostWithoutTax = $this->context->cart->getTotalShippingCost(null, false);
            
            if (!empty($products)) {
                $lastProduct = end($products);
                $threshold = $this->getFreeShippingThreshold($lastProduct['id_product']);
                $this->context->smarty->assign([
                    'free_shipping_threshold' => $threshold,
                    'modal_product_id' => $lastProduct['id_product']
                ]);
            }
        }

        $this->context->controller->registerStylesheet(
            'relatedproducts-modal',
            'modules/'.$this->name.'/views/css/relatedproducts-modal.css',
            [
                'media' => 'all',
                'priority' => 50
            ]
        );

        $this->context->controller->registerJavascript(
            'module-relatedproducts-js',
            'modules/' . $this->name . '/views/js/relatedproducts-cart.js',
            ['position' => 'bottom', 'priority' => 150]
        );
        $this->context->controller->registerJavascript(
            'module-relatedproducts-carousel-js',
            'modules/' . $this->name . '/views/js/relatedproducts.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    public function uninstall()
    {
        return parent::uninstall();
    }



    /**
     * Called by hook engine - for related products
     */
    public function renderWidget($hookName, array $configuration)
    {
        $variables = $this->getWidgetVariables($hookName, $configuration);
        
        // Add free shipping threshold to variables
        if (isset($configuration['id_product'])) {
            $variables['free_shipping_threshold'] = $this->getFreeShippingThreshold($configuration['id_product']);
        }
        
        $this->smarty->assign($variables);
        return $this->fetch('module:relatedproducts/views/templates/hook/displayProductModal.tpl');
    }

    /**
     * Gets free shipping threshold for product (for use in template)
     */
    public static function getProductFreeShippingThreshold($id_product)
    {
        $module = new self();
        return $module->getFreeShippingThreshold($id_product);
    }

    /**
     * Returns data for view (e.g. list of related products)
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
        if (!isset($configuration['id_product'])) {
            return [];
        }

        $id_product = (int)$configuration['id_product'];
        $id_lang = (int)$this->context->language->id;

        $product = new Product($id_product, false, $id_lang);
        if (!Validate::isLoadedObject($product)) {
            return [
                'related_products' => [],
                'recommendation_type' => 'accessories',
            ];
        }
        $accessories = $product->getAccessories($id_lang);
        $recommendationType = 'accessories'; // Typ rekomendacji

        // If no accessories, fetch products from the same category
        if (empty($accessories)) {
            $accessories = $this->getProductsFromSameCategory($id_product, $id_lang);
            $recommendationType = 'category';
        }

        // For category products, use different data fetching method
        if ($recommendationType === 'category') {
            $products = $this->formatCategoryProducts($accessories, $id_lang);
        } else {
            $products = Product::getProductsProperties($id_lang, $accessories);
            
            // Format prices for accessories in the same way as for category products
            foreach ($products as &$related) {
                // Format prices
                if (isset($related['price'])) {
                    $related['price'] = Tools::displayPrice($related['price']);
                }
                if (isset($related['price_tax_exc'])) {
                    $related['price_tax_exc'] = Tools::displayPrice($related['price_tax_exc']);
                }
                
                $imageId = null;

                // Prefer 'cover_image_id' if set
                if (!empty($related['cover_image_id'])) {
                    $imageId = (int) $related['cover_image_id'];
                } elseif (!empty($related['id_image'])) {
                    $imageId = (int) explode('-', $related['id_image'])[0];
                }

                if ($imageId) {
                    $related['image_url'] = $this->context->link->getImageLink(
                        $related['link_rewrite'],
                        $imageId,
                        'home_default'
                    );
                } else {
                    $related['image_url'] = $this->context->link->getImageLink(
                        'default',
                        null,
                        'home_default'
                    );
                }
            }
        }
        return [
            'related_products' => $products,
            'recommendation_type' => $recommendationType,
        ];
    }

    /**
     * Fetches products from the same category as given product
     */
    private function getProductsFromSameCategory($id_product, $id_lang, $limit = 8)
    {
        $product = new Product($id_product, false, $id_lang);
        $categories = $product->getCategories();
        
        if (empty($categories)) {
            return [];
        }

        // Select main category (first on list)
        $main_category = $categories[0];
        
        // Get products from this category (excluding current product)
        // Using standard PrestaShop function with additional conditions
        $sql = new DbQuery();
    $context = Context::getContext();
    $idShop = (int) $context->shop->id;
    $idShopGroup = (int) $context->shop->id_shop_group;

    $sql->select('p.id_product, pl.name, pl.link_rewrite');
        $sql->from('product', 'p');
        $sql->innerJoin('category_product', 'cp', 'p.id_product = cp.id_product');
    $sql->innerJoin('product_shop', 'pshop', 'p.id_product = pshop.id_product AND pshop.id_shop = ' . $idShop);
    $sql->innerJoin('product_lang', 'pl', 'p.id_product = pl.id_product AND pl.id_lang = ' . (int)$id_lang . ' AND pl.id_shop = ' . $idShop);
    $sql->leftJoin('stock_available', 'stock', 'p.id_product = stock.id_product AND stock.id_product_attribute = 0 AND (stock.id_shop = ' . $idShop . ' OR stock.id_shop = 0) AND (stock.id_shop_group = ' . $idShopGroup . ' OR stock.id_shop_group = 0)');
        $sql->where('cp.id_category = ' . (int)$main_category);
        $sql->where('p.id_product != ' . (int)$id_product);
        $sql->where('p.active = 1');
    $sql->where('pshop.active = 1');
    $sql->where('pl.name IS NOT NULL AND pl.name != ""');
    $sql->orderBy('stock.quantity DESC, RAND()');
        $sql->limit($limit);

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
        if (!$results) {
            return [];
        }

        // Return products in format compatible with getAccessories()
        $categoryProducts = [];
        foreach ($results as $row) {
            $categoryProducts[] = [
                'id_product' => (int)$row['id_product'],
                'name' => $row['name'],
                'link_rewrite' => $row['link_rewrite']
            ];
        }
        
        return $categoryProducts;
    }

    /**
     * Formats category product data for display
     */
    private function formatCategoryProducts($categoryProducts, $id_lang)
    {
        $formattedProducts = [];
        
        foreach ($categoryProducts as $productData) {
            $id_product = (int)$productData['id_product'];
            $product = new Product($id_product, true, $id_lang);
            
            if (!Validate::isLoadedObject($product)) {
                continue; // Skip invalid products
            }

            $link = $this->context->link->getProductLink($product);
            $price = Product::getPriceStatic($id_product, true, null, 6, null, false, true, 1, true);
            
            // Get main product image
            $cover = Product::getCover($id_product);
            $imageUrl = '';
            if ($cover) {
                $imageUrl = $this->context->link->getImageLink(
                    $product->link_rewrite,
                    $cover['id_image'],
                    'home_default'
                );
            } else {
                $imageUrl = $this->context->link->getImageLink(
                    'default',
                    null,
                    'home_default'
                );
            }

            $formattedProducts[] = [
                'id_product' => $id_product,
                'name' => $product->name,
                'link' => $link,
                'link_rewrite' => $product->link_rewrite,
                'price' => Tools::displayPrice($price),
                'price_tax_exc' => Tools::displayPrice(Product::getPriceStatic($id_product, false)),
                'image_url' => $imageUrl,
                'cover' => $cover
            ];
        }
        
        return $formattedProducts;
    }

    /**
     * Gets lowest carrier price for given product
     */
    public function getLowestShippingPrice($id_product)
    {
        $context = Context::getContext();
        $product = new Product($id_product, false, $context->language->id);
        
        if (!Validate::isLoadedObject($product)) {
            return [
                'price' => 2.46,
                'formatted_price' => Tools::displayPrice(2.46),
                'carrier' => ['name' => 'Standard delivery']
            ];
        }

        // Get ADDITIONAL shipping cost from product
        $additionalShippingCost = (float)$product->additional_shipping_cost;
        
        // Get product price for price ranges
        $productPrice = Product::getPriceStatic($id_product, true);
        $db = Db::getInstance();
        
        // Check if product has assigned specific carriers
        $productWeight = (float)$product->weight;
        
        $carriersQuery = '
            SELECT c.id_carrier, c.id_reference, c.name, c.active,
                   COALESCE(rw.id_range_weight, rp.id_range_price) AS range_id,
                   CASE
                       WHEN rw.id_range_weight IS NOT NULL THEN "weight"
                       WHEN rp.id_range_price IS NOT NULL THEN "price"
                       ELSE "unknown"
                   END AS range_type,
                   COALESCE(rw.delimiter1, rp.delimiter1) AS range_from,
                   COALESCE(rw.delimiter2, rp.delimiter2) AS range_to,
                   d.price AS delivery_price,
                   z.name AS zone_name
            FROM '._DB_PREFIX_.'carrier c
            INNER JOIN '._DB_PREFIX_.'product_carrier pc 
                ON pc.id_carrier_reference = c.id_reference
            INNER JOIN '._DB_PREFIX_.'delivery d 
                ON d.id_carrier = c.id_carrier
            LEFT JOIN '._DB_PREFIX_.'range_weight rw 
                ON rw.id_range_weight = d.id_range_weight
            LEFT JOIN '._DB_PREFIX_.'range_price rp 
                ON rp.id_range_price = d.id_range_price
            LEFT JOIN '._DB_PREFIX_.'zone z 
                ON z.id_zone = d.id_zone
            WHERE pc.id_product = '.(int)$id_product.'
              AND c.active = 1
              AND c.deleted = 0
              AND d.id_zone = 1
              AND (
                  (rw.id_range_weight IS NOT NULL AND '.(float)$productWeight.' >= rw.delimiter1 AND '.(float)$productWeight.' < rw.delimiter2) OR
                  (rp.id_range_price IS NOT NULL AND '.(float)$productPrice.' >= rp.delimiter1 AND '.(float)$productPrice.' < rp.delimiter2) OR
                  (rw.id_range_weight IS NULL AND rp.id_range_price IS NULL)
              )
            ORDER BY c.name, range_type, range_from
        ';

        $assignedCarriers = $db->executeS($carriersQuery);

        // If product has no assigned carriers, use all available
        if (empty($assignedCarriers)) {
            $carriersQuery = '
                SELECT c.id_carrier, c.id_reference, c.name, c.active,
                       COALESCE(rw.id_range_weight, rp.id_range_price) AS range_id,
                       CASE
                           WHEN rw.id_range_weight IS NOT NULL THEN "weight"
                           WHEN rp.id_range_price IS NOT NULL THEN "price"
                           ELSE "unknown"
                       END AS range_type,
                       COALESCE(rw.delimiter1, rp.delimiter1) AS range_from,
                       COALESCE(rw.delimiter2, rp.delimiter2) AS range_to,
                       d.price AS delivery_price,
                       z.name AS zone_name
                FROM '._DB_PREFIX_.'carrier c
                INNER JOIN '._DB_PREFIX_.'delivery d 
                    ON d.id_carrier = c.id_carrier
                LEFT JOIN '._DB_PREFIX_.'range_weight rw 
                    ON rw.id_range_weight = d.id_range_weight
                LEFT JOIN '._DB_PREFIX_.'range_price rp 
                    ON rp.id_range_price = d.id_range_price
                LEFT JOIN '._DB_PREFIX_.'zone z 
                    ON z.id_zone = d.id_zone
                WHERE c.active = 1 
                  AND c.deleted = 0
                  AND d.id_zone = 1
                  AND (
                      (rw.id_range_weight IS NOT NULL AND '.(float)$productWeight.' >= rw.delimiter1 AND '.(float)$productWeight.' < rw.delimiter2) OR
                      (rp.id_range_price IS NOT NULL AND '.(float)$productPrice.' >= rp.delimiter1 AND '.(float)$productPrice.' < rp.delimiter2) OR
                      (rw.id_range_weight IS NULL AND rp.id_range_price IS NULL)
                  )
                ORDER BY c.name, range_type, range_from
            ';
            $assignedCarriers = $db->executeS($carriersQuery);
        }
        
        // Get customcarrier reference to identify customcarrier-managed carriers
        $customCarrierId = (int) Configuration::get('CUSTOMCARRIER_ID_CARRIER');
        $customCarrierRef = 0;
        $customCarrierDefaultCost = 0.0;
        if ($customCarrierId > 0) {
            $ccCarrier = new Carrier($customCarrierId);
            if (Validate::isLoadedObject($ccCarrier)) {
                $customCarrierRef = (int) $ccCarrier->id_reference;
            }
            $customCarrierDefaultCost = (float) Configuration::get('CUSTOMCARRIER_DEFAULT_COST');
        }

        // PRIORITY 1: Check if product has base_shipping_cost > 0 in customcarrier
        // If yes, return this value immediately without checking other carriers
        $customCarrierProductCost = $db->getValue('
            SELECT base_shipping_cost
            FROM '._DB_PREFIX_.'customcarrier_product
            WHERE id_product = '.(int)$id_product.'
        ');

        if ($customCarrierProductCost !== false && (float)$customCarrierProductCost > 0) {
            $baseCost = (float)$customCarrierProductCost;

            return [
                'price' => $baseCost,
                'formatted_price' => Tools::displayPrice($baseCost),
                'carrier' => [
                    'id_carrier' => $customCarrierId,
                    'name' => 'Kurier Norwit 01'
                ],
                'debug_carriers' => [[
                    'carrier_name' => 'Kurier Norwit 01',
                    'carrier_id' => $customCarrierId,
                    'price' => $baseCost,
                    'used_method' => 'customcarrier_priority',
                    'customcarrier_base_cost' => $customCarrierProductCost
                ]]
            ];
        }

        // PRIORITY 2: No custom cost set - find lowest price from all carriers
        $debugInfo = [];
        $lowestPrice = null;
        $lowestCarrier = null;
        $lowestIsCustomCarrier = false;

        foreach ($assignedCarriers as $carrier) {
            try {
                // Skip in-store pickup options - we only want carriers
                $isPickup = (
                    stripos($carrier['name'], 'sklep') !== false ||
                    stripos($carrier['name'], 'pickup') !== false ||
                    stripos($carrier['name'], 'odbiór') !== false ||
                    stripos($carrier['name'], 'magazyn') !== false ||
                    stripos($carrier['name'], 'self') !== false
                );

                if ($isPickup) {
                    // Skip pickup options - looking only for carriers
                    $debugInfo[] = [
                        'carrier_name' => $carrier['name'],
                        'carrier_id' => $carrier['id_carrier'],
                        'skipped_reason' => 'pickup_option'
                    ];
                    continue;
                }

                // Use price directly from query
                $shippingCost = (float)$carrier['delivery_price'];
                $debugQuery = [
                    'product_price' => $productPrice,
                    'product_weight' => $productWeight,
                    'range_type' => $carrier['range_type'],
                    'range_from_to' => $carrier['range_from'] . ' - ' . $carrier['range_to'],
                    'delivery_price' => $carrier['delivery_price'],
                    'zone' => $carrier['zone_name'],
                    'used_method' => 'unified_range_query'
                ];
                
                // Check if this carrier is managed by customcarrier module
                $isCustomCarrier = ($customCarrierRef > 0 && (int)$carrier['id_reference'] === $customCarrierRef);

                // Check if we have price from query
                if ($shippingCost > 0) {
                    $delivery = $shippingCost;
                    $debugQuery['used_method'] = 'range_price_query';
                } elseif ($isCustomCarrier) {
                    // Customcarrier uses external shipping - native delivery table has price 0
                    // Priority 1: Check if product has base_shipping_cost > 0 in customcarrier_product
                    $customCarrierProductCost = $db->getValue('
                        SELECT base_shipping_cost
                        FROM '._DB_PREFIX_.'customcarrier_product
                        WHERE id_product = '.(int)$id_product.'
                    ');

                    if ($customCarrierProductCost !== false && (float)$customCarrierProductCost > 0) {
                        // Product has custom base_shipping_cost set - use it
                        $delivery = (float)$customCarrierProductCost;
                        $debugQuery['used_method'] = 'customcarrier_product_cost';
                        $debugQuery['customcarrier_base_cost'] = $customCarrierProductCost;
                    } else {
                        // Priority 2: No custom cost - find lowest price from other carriers
                        $lowestOtherCarrierPrice = $db->getValue('
                            SELECT MIN(d.price)
                            FROM '._DB_PREFIX_.'delivery d
                            INNER JOIN '._DB_PREFIX_.'carrier c ON d.id_carrier = c.id_carrier
                            WHERE c.active = 1
                              AND c.deleted = 0
                              AND d.id_zone = 1
                              AND d.price > 0
                              AND c.id_carrier != '.(int)$customCarrierId.'
                        ');

                        if ($lowestOtherCarrierPrice && (float)$lowestOtherCarrierPrice > 0) {
                            $delivery = (float)$lowestOtherCarrierPrice;
                            $debugQuery['used_method'] = 'lowest_other_carrier';
                            $debugQuery['lowest_other_price'] = $lowestOtherCarrierPrice;
                        } else {
                            // Fallback to default cost if no other carriers available
                            $delivery = $customCarrierDefaultCost > 0 ? $customCarrierDefaultCost : 0;
                            $debugQuery['used_method'] = 'customcarrier_fallback_default';
                        }
                    }
                } else {
                    // Fallback: try to get price without considering range
                    try {
                        $fallbackPrice = $db->getValue('
                            SELECT MIN(d.price)
                            FROM '._DB_PREFIX_.'delivery d
                            WHERE d.id_carrier = '.(int)$carrier['id_carrier'].'
                            AND d.id_zone = 1
                            AND d.price > 0
                        ');

                        if ($fallbackPrice && $fallbackPrice > 0) {
                            $delivery = (float)$fallbackPrice;
                            $debugQuery['used_method'] = 'fallback_min_price';
                            $debugQuery['fallback_price'] = $fallbackPrice;
                        } else {
                            // If still no data, use default value
                            $delivery = 2.46;
                            $debugQuery['used_method'] = 'absolute_fallback';
                        }
                    } catch (Exception $e) {
                        $delivery = 2.46;
                        $debugQuery['used_method'] = 'error_fallback';
                        $debugQuery['fallback_error'] = $e->getMessage();
                    }
                }
                
                $debugQuery['delivery_final'] = $delivery;
                
                // Using already calculated price
                $shippingCost = (float)$delivery;

                // Zapisz info do debugowania (opcjonalne)
                $debugInfo[] = [
                    'carrier_name' => $carrier['name'],
                    'carrier_id' => $carrier['id_carrier'],
                    'price' => $shippingCost,
                    'additional_shipping_cost' => $additionalShippingCost,
                    'product_weight' => $productWeight ?? 0,
                    'debug_queries' => $debugQuery
                ];

                // Update lowest price
                if ($lowestPrice === null || $shippingCost < $lowestPrice) {
                    $lowestPrice = $shippingCost;
                    $lowestCarrier = $carrier;
                    $lowestIsCustomCarrier = $isCustomCarrier;
                }

                } catch (Exception $e) {
                $debugInfo[] = [
                    'carrier_name' => $carrier['name'],
                    'carrier_id' => $carrier['id_carrier'],
                    'error' => $e->getMessage(),
                    'line' => $e->getLine()
                ];
                continue;
            }
        }

        // Fallback if no carriers found
        if ($lowestPrice === null) {
            $lowestPrice = 2.46;
            $lowestCarrier = ['name' => 'Standard delivery'];
        }

        // Check if heavy products (>20kg) should use pallet carrier
        $productWeight = (float)$product->weight;
        if ($productWeight > 20) {
            // Find pallet carrier
            foreach ($debugInfo as $carrierInfo) {
                if (stripos($carrierInfo['carrier_name'], 'paletowa') !== false && isset($carrierInfo['price'])) {
                    $lowestPrice = $carrierInfo['price'];
                    $lowestCarrier = ['name' => $carrierInfo['carrier_name']];
                    break;
                }
            }
        }

        // Add 23% VAT to carrier price
        $vatRate = 1.23; // 23% VAT
        $priceWithVat = $lowestPrice * $vatRate;

        // For customcarrier-managed carriers, skip additional_shipping_cost
        // (customcarrier handles full shipping cost externally, PS doesn't add it for external carriers)
        if ($lowestIsCustomCarrier) {
            $additionalShippingCost = 0;
        }

        // ADD additional shipping costs
        $additionalShippingCostWithVat = $additionalShippingCost * $vatRate;
        $finalPrice = $priceWithVat + $additionalShippingCostWithVat;

        return [
            'price' => $finalPrice,
            'price_without_vat' => $lowestPrice + $additionalShippingCost,
            'carrier_price_with_vat' => $priceWithVat,
            'additional_shipping_cost' => $additionalShippingCostWithVat,
            'formatted_price' => Tools::displayPrice($finalPrice),
            'carrier' => $lowestCarrier,
            'debug_carriers' => $debugInfo,
            'product_id' => $id_product,
            'vat_applied' => true,
            'product_weight' => $productWeight
        ];
    }

    /**
     * Gets free shipping threshold from price ranges.
     * If product has a carrier assigned via customcarrier module, reads from customcarrier_zone table.
     * Otherwise falls back to global settings from delivery table.
     */
    public function getFreeShippingThreshold($id_product = null)
    {
        $db = Db::getInstance();

        // If specific product provided, check its assigned carriers
        if ($id_product) {
            // Check if product has a carrier managed by customcarrier module
            $customCarrierId = (int) Configuration::get('CUSTOMCARRIER_ID_CARRIER');
            if ($customCarrierId > 0) {
                $carrier = new Carrier($customCarrierId);
                if (Validate::isLoadedObject($carrier)) {
                    $customCarrierRef = (int) $carrier->id_reference;

                    // Check if this product is assigned to the customcarrier
                    $hasCustomCarrier = $db->getValue('
                        SELECT COUNT(*)
                        FROM '._DB_PREFIX_.'product_carrier pc
                        WHERE pc.id_product = '.(int)$id_product.'
                          AND pc.id_carrier_reference = '.$customCarrierRef.'
                    ');

                    if ($hasCustomCarrier) {
                        // Read threshold from customcarrier_zone table
                        $customThreshold = $db->getValue('
                            SELECT threshold_amount
                            FROM '._DB_PREFIX_.'customcarrier_zone
                            WHERE id_zone = 1
                              AND active = 1
                        ');

                        if ($customThreshold && (float)$customThreshold > 0) {
                            return (float)$customThreshold;
                        }
                    }
                }
            }

            // Product has carriers but not customcarrier - check delivery table
            $productCarriersQuery = '
                SELECT MIN(COALESCE(rw.delimiter1, rp.delimiter1)) AS free_threshold
                FROM '._DB_PREFIX_.'delivery d
                INNER JOIN '._DB_PREFIX_.'carrier c ON d.id_carrier = c.id_carrier
                INNER JOIN '._DB_PREFIX_.'product_carrier pc ON pc.id_carrier_reference = c.id_reference
                LEFT JOIN '._DB_PREFIX_.'range_weight rw ON rw.id_range_weight = d.id_range_weight
                LEFT JOIN '._DB_PREFIX_.'range_price rp ON rp.id_range_price = d.id_range_price
                WHERE pc.id_product = '.(int)$id_product.'
                  AND c.active = 1
                  AND c.deleted = 0
                  AND d.id_zone = 1
                  AND d.price = 0
                  AND (rw.delimiter1 > 0 OR rp.delimiter1 > 0)
            ';

            $productThreshold = $db->getValue($productCarriersQuery);

            if ($productThreshold && $productThreshold > 0) {
                return (float)$productThreshold;
            }
        }

        // Global fallback: lowest threshold from delivery table (any active carrier)
        $query = '
            SELECT MIN(COALESCE(rw.delimiter1, rp.delimiter1)) AS free_threshold
            FROM '._DB_PREFIX_.'delivery d
            INNER JOIN '._DB_PREFIX_.'carrier c ON d.id_carrier = c.id_carrier
            LEFT JOIN '._DB_PREFIX_.'range_weight rw ON rw.id_range_weight = d.id_range_weight
            LEFT JOIN '._DB_PREFIX_.'range_price rp ON rp.id_range_price = d.id_range_price
            WHERE c.active = 1
              AND c.deleted = 0
              AND d.id_zone = 1
              AND d.price = 0
              AND (rw.delimiter1 > 0 OR rp.delimiter1 > 0)
        ';

        $threshold = $db->getValue($query);
        if (!$threshold || $threshold <= 0) {
            $prestashop_free_shipping = Configuration::get('PS_SHIPPING_FREE_PRICE');
            if ($prestashop_free_shipping && $prestashop_free_shipping > 0) {
                return (float)$prestashop_free_shipping;
            }

            return 0.0;
        }
        return (float)$threshold;
    }

    /**
     * AJAX endpoint for getting carrier information
     */
    public function getShippingInfo($id_product)
    {
        header('Content-Type: application/json');

        $db = Db::getInstance();
        $context = Context::getContext();

        // DEBUG MODE - add ?debug=1 to URL to see debug info
        $debugMode = (bool) Tools::getValue('debug', 0);

        // Get wizard cost for customcarrier based on cart total
        $wizardCost = $this->getWizardShippingCost($context);
        $wizardCostWithTax = $wizardCost * 1.23; // Add 23% VAT

        // Get cart total for debugging
        $cartTotal = 0.0;
        if ($context->cart && $context->cart->id) {
            $cartTotal = (float) $context->cart->getOrderTotal(true, 4);
        }

        // Get product settings from customcarrier
        $productSettings = $db->getRow('
            SELECT
                base_shipping_cost,
                free_shipping,
                free_shipping_quantity,
                free_shipping_from_price,
                apply_threshold,
                separate_package,
                exclude_from_free_shipping,
                multiply_by_quantity,
                max_quantity_per_package,
                max_weight_per_package,
                max_packages,
                cost_above_max_packages
            FROM '._DB_PREFIX_.'customcarrier_product
            WHERE id_product = '.(int)$id_product.'
        ');

        // Check if product has ACTIVE custom settings
        $useCustomSettings = false;
        $hasFreeShippingFromPrice = false;

        if ($productSettings) {
            $baseCost = (float)($productSettings['base_shipping_cost'] ?? 0);
            $freeShipping = !empty($productSettings['free_shipping']);
            $freeShippingQty = (int)($productSettings['free_shipping_quantity'] ?? 0);
            $freeShippingFromPrice = (float)($productSettings['free_shipping_from_price'] ?? 0);
            $applyThreshold = !empty($productSettings['apply_threshold']);
            $separatePackage = !empty($productSettings['separate_package']);
            $excludedFromFree = !empty($productSettings['exclude_from_free_shipping']);
            $multiplyByQty = !empty($productSettings['multiply_by_quantity']);
            $maxQtyPerPackage = (int)($productSettings['max_quantity_per_package'] ?? 0);
            $maxWeightPerPackage = (float)($productSettings['max_weight_per_package'] ?? 0);
            $maxPackages = (int)($productSettings['max_packages'] ?? 0);
            $costAboveMaxPackages = (float)($productSettings['cost_above_max_packages'] ?? 0);

            // Check if product qualifies for free shipping from price threshold
            if ($freeShippingFromPrice > 0 && !$excludedFromFree) {
                $productPrice = Product::getPriceStatic($id_product, true); // brutto
                if ($productPrice >= $freeShippingFromPrice) {
                    $hasFreeShippingFromPrice = true;
                }
            }

            // Check if product has any active custom configuration
            $useCustomSettings = $baseCost > 0 || $freeShipping || $freeShippingQty > 0 ||
                                 $freeShippingFromPrice > 0 || $applyThreshold || $separatePackage ||
                                 $excludedFromFree || $multiplyByQty || $maxQtyPerPackage > 0 ||
                                 $maxWeightPerPackage > 0;

            // If baseCost is 0 but product is excluded from free shipping or has separate_package,
            // use lowest carrier price as fallback (these products SHOULD have a shipping cost)
            if ($baseCost <= 0 && ($excludedFromFree || $separatePackage)) {
                $lowestShipping = $this->getLowestShippingPrice($id_product);
                if ($lowestShipping && isset($lowestShipping['price'])) {
                    $baseCost = (float) $lowestShipping['price'];
                } else {
                    $baseCost = $wizardCostWithTax; // Fallback to wizard if no carriers found
                }
            }
            // For other cases (multiply_by_quantity, max_quantity_per_package, etc.),
            // if baseCost = 0, we respect that value (0 × quantity = 0)
        }

        // Check if cart has products that block free shipping (exclude_from_free_shipping or separate_package)
        $cartHasProductsBlockingFreeShipping = false;
        $blockingProductCost = 0;
        if ($context->cart && $context->cart->id) {
            $cartProducts = $context->cart->getProducts();
            foreach ($cartProducts as $cartProduct) {
                $cartProductSettings = $db->getRow('
                    SELECT exclude_from_free_shipping, separate_package, base_shipping_cost
                    FROM '._DB_PREFIX_.'customcarrier_product
                    WHERE id_product = '.(int)$cartProduct['id_product'].'
                ');
                if ($cartProductSettings) {
                    $isExcluded = !empty($cartProductSettings['exclude_from_free_shipping']);
                    $isSeparate = !empty($cartProductSettings['separate_package']);
                    if ($isExcluded || $isSeparate) {
                        $cartHasProductsBlockingFreeShipping = true;
                        // Get the cost for this blocking product
                        $productCost = (float)($cartProductSettings['base_shipping_cost'] ?? 0);
                        if ($productCost <= 0) {
                            // No base cost set - get from carrier price ranges
                            $lowestCarrierPrice = $this->getLowestShippingPrice((int)$cartProduct['id_product']);
                            $productCost = ($lowestCarrierPrice && isset($lowestCarrierPrice['price']))
                                ? (float)$lowestCarrierPrice['price']
                                : $wizardCostWithTax;
                        }
                        if ($productCost > $blockingProductCost) {
                            $blockingProductCost = $productCost;
                        }
                    }
                }
            }
        }

        // If no custom settings, find lowest price from ALL active carriers
        if (!$useCustomSettings) {
            // First check if cart reached free shipping threshold (wizard returns 0)
            // BUT only return free if no products in cart are blocking free shipping
            if ($wizardCost == 0 && !$cartHasProductsBlockingFreeShipping) {
                $response = [
                    'success' => true,
                    'lowest_price' => 0,
                    'formatted_price' => 'Za darmo!',
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => 'threshold_reached_no_settings',
                    'is_free_shipping' => true,
                    'cart_has_additional_costs' => false,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => $this->getFreeShippingThreshold($id_product),
                    'shipping_info' => [
                        'type' => 'free',
                        'reason' => 'cart_threshold_reached'
                    ]
                ];
                if ($debugMode) {
                    $response['debug'] = [
                        'cart_total' => $cartTotal,
                        'wizard_cost' => $wizardCost,
                        'use_custom_settings' => $useCustomSettings,
                        'product_settings' => $productSettings,
                    ];
                }
                echo json_encode($response);
                exit;
            }

            // Cart reached threshold BUT has blocking products - show the blocking product cost
            if ($wizardCost == 0 && $cartHasProductsBlockingFreeShipping) {
                echo json_encode([
                    'success' => true,
                    'lowest_price' => $blockingProductCost,
                    'formatted_price' => Tools::displayPrice($blockingProductCost),
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => 'threshold_blocked_by_cart_products',
                    'is_free_shipping' => false,
                    'cart_has_additional_costs' => true,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => $this->getFreeShippingThreshold($id_product),
                    'shipping_info' => [
                        'type' => 'paid',
                        'reason' => 'cart_has_products_blocking_free_shipping',
                        'blocking_product_cost' => $blockingProductCost
                    ]
                ]);
                exit;
            }

            $lowestShipping = $this->getLowestShippingPrice($id_product);

            if ($lowestShipping && isset($lowestShipping['price'])) {
                $lowestPrice = (float) $lowestShipping['price'];
                // Only consider free if price is 0 AND no blocking products in cart
                $isFree = ($lowestPrice == 0) && !$cartHasProductsBlockingFreeShipping;
                $carrierName = $lowestShipping['carrier']['name'] ?? 'Wysyłka';

                // If would be free but cart has blocking products, show blocking product cost
                $effectivePrice = $lowestPrice;
                if ($lowestPrice == 0 && $cartHasProductsBlockingFreeShipping) {
                    $effectivePrice = $blockingProductCost;
                }

                $response = [
                    'success' => true,
                    'lowest_price' => $effectivePrice,
                    'formatted_price' => $isFree ? 'Za darmo!' : Tools::displayPrice($effectivePrice),
                    'carrier_name' => $carrierName,
                    'calculation_method' => 'lowest_carrier',
                    'is_free_shipping' => $isFree,
                    'cart_has_additional_costs' => $cartHasProductsBlockingFreeShipping,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => $this->getFreeShippingThreshold($id_product),
                    'shipping_info' => [
                        'type' => 'lowest_carrier',
                        'price' => $effectivePrice
                    ]
                ];
                if ($debugMode) {
                    $response['debug'] = [
                        'cart_total' => $cartTotal,
                        'wizard_cost' => $wizardCost,
                        'lowest_price_raw' => $lowestPrice,
                        'use_custom_settings' => $useCustomSettings,
                        'product_settings' => $productSettings,
                        'lowest_shipping_data' => $lowestShipping,
                    ];
                }
                echo json_encode($response);
                exit;
            }

            // Fallback to wizard cost if getLowestShippingPrice fails
            // Only consider free if wizard cost is 0 AND no blocking products in cart
            $isFree = ($wizardCost == 0) && !$cartHasProductsBlockingFreeShipping;
            $effectivePrice = $wizardCostWithTax;
            if ($wizardCost == 0 && $cartHasProductsBlockingFreeShipping) {
                $effectivePrice = $blockingProductCost;
            }

            echo json_encode([
                'success' => true,
                'lowest_price' => $effectivePrice,
                'formatted_price' => $isFree ? 'Za darmo!' : Tools::displayPrice($effectivePrice),
                'carrier_name' => 'Kurier Norwit 01',
                'calculation_method' => 'wizard_fallback',
                'is_free_shipping' => $isFree,
                'cart_has_additional_costs' => $cartHasProductsBlockingFreeShipping,
                'product_id' => $id_product,
                'shipping_info' => [
                    'type' => 'wizard_fallback',
                    'wizard_cost' => $wizardCost
                ]
            ]);
            exit;
        }

        if ($useCustomSettings) {

            // Get cart quantity and cart total for this product (used in multiple cases)
            $cartQuantity = 0;
            $cartTotal = 0;
            $context = Context::getContext();
            if ($context->cart) {
                $cartProducts = $context->cart->getProducts();
                foreach ($cartProducts as $cartProduct) {
                    if ((int)$cartProduct['id_product'] === (int)$id_product) {
                        $cartQuantity += (int)$cartProduct['cart_quantity'];
                    }
                }
                // Get cart total (with tax)
                $cartTotal = (float)$context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
            }

            // Get free shipping threshold for this product
            $freeShippingThreshold = $this->getFreeShippingThreshold($id_product);

            // Calculate display cost based on settings
            $displayCost = $baseCost;
            $numPackages = 1;
            $extraPackages = 0;

            if ($cartQuantity > 0) {
                // If max_quantity_per_package is set, calculate number of packages by quantity
                if ($maxQtyPerPackage > 0) {
                    $numPackages = (int)ceil($cartQuantity / $maxQtyPerPackage);

                    // If max_packages is set and exceeded, use flat pallet cost
                    // Example: max 2 pcs/package, max 2 packages at 60 PLN each, but 5+ pcs (3 packages) = 140 PLN total (pallet)
                    if ($maxPackages > 0 && $numPackages > $maxPackages && $costAboveMaxPackages > 0) {
                        // Flat pallet cost replaces all package costs
                        $displayCost = $costAboveMaxPackages;
                        $extraPackages = $numPackages - $maxPackages;
                    } else {
                        $displayCost = $baseCost * $numPackages;
                    }
                // If max_weight_per_package is set, calculate number of packages by weight
                } elseif ($maxWeightPerPackage > 0) {
                    $productObj = new Product($id_product, false);
                    $productWeight = (float) $productObj->weight;
                    if ($productWeight > 0) {
                        $totalWeight = $productWeight * $cartQuantity;
                        $numPackages = (int)ceil($totalWeight / $maxWeightPerPackage);

                        // If max_packages is set and exceeded, use flat pallet cost
                        if ($maxPackages > 0 && $numPackages > $maxPackages && $costAboveMaxPackages > 0) {
                            $displayCost = $costAboveMaxPackages;
                            $extraPackages = $numPackages - $maxPackages;
                        } else {
                            $displayCost = $baseCost * $numPackages;
                        }
                    }
                } elseif ($multiplyByQty) {
                    // No package limit, but multiply by quantity
                    $displayCost = $baseCost * $cartQuantity;
                }
            }

            // base_shipping_cost is entered as BRUTTO in admin - no VAT conversion needed
            $displayCostWithTax = $displayCost;

            // CHECK: If apply_threshold is enabled AND cart total >= threshold → FREE SHIPPING
            // (but only if NOT excluded from free shipping and NOT separate_package)
            if ($applyThreshold && !$excludedFromFree && !$separatePackage && $freeShippingThreshold > 0) {
                if ($cartTotal >= $freeShippingThreshold) {
                    echo json_encode([
                        'success' => true,
                        'lowest_price' => 0,
                        'formatted_price' => 'Za darmo!',
                        'carrier_name' => 'Kurier Norwit 01',
                        'calculation_method' => 'threshold_reached',
                        'is_free_shipping' => true,
                        'product_id' => $id_product,
                        'free_shipping_threshold' => $freeShippingThreshold,
                        'shipping_info' => [
                            'type' => 'free',
                            'reason' => 'cart_threshold_reached',
                            'cart_total' => $cartTotal,
                            'threshold' => $freeShippingThreshold
                        ]
                    ]);
                    exit;
                }
            }

            // CASE 1: Product has free_shipping = 1 AND is NOT excluded
            // (works regardless of separate_package)
            if ($freeShipping && !$excludedFromFree) {
                echo json_encode([
                    'success' => true,
                    'lowest_price' => 0,
                    'formatted_price' => 'Za darmo!',
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => 'free_shipping',
                    'is_free_shipping' => true,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => $this->getFreeShippingThreshold($id_product),
                    'shipping_info' => [
                        'type' => 'free',
                        'reason' => 'free_shipping_enabled'
                    ]
                ]);
                exit;
            }

            // CASE 2: Product has free_shipping_quantity threshold (free from X pcs)
            // (works regardless of separate_package)
            if ($freeShippingQty > 0 && !$excludedFromFree) {
                // If cart quantity >= threshold, shipping is FREE
                if ($cartQuantity >= $freeShippingQty) {
                    echo json_encode([
                        'success' => true,
                        'lowest_price' => 0,
                        'formatted_price' => 'Za darmo!',
                        'carrier_name' => 'Kurier Norwit 01',
                        'calculation_method' => 'free_shipping_quantity_reached',
                        'is_free_shipping' => true,
                        'product_id' => $id_product,
                        'free_shipping_threshold' => 0,
                        'shipping_info' => [
                            'type' => 'free',
                            'reason' => 'quantity_threshold_reached',
                            'cart_quantity' => $cartQuantity,
                            'required_quantity' => $freeShippingQty
                        ]
                    ]);
                    exit;
                }
            }

            // CASE 3: Product has free_shipping_from_price threshold (free when product price >= X)
            // (works regardless of separate_package)
            if ($hasFreeShippingFromPrice) {
                echo json_encode([
                    'success' => true,
                    'lowest_price' => 0,
                    'formatted_price' => 'Za darmo!',
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => 'free_shipping_from_price_reached',
                    'is_free_shipping' => true,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => 0,
                    'shipping_info' => [
                        'type' => 'free',
                        'reason' => 'product_price_threshold_reached',
                        'product_price' => Product::getPriceStatic($id_product, true),
                        'required_price' => $freeShippingFromPrice
                    ]
                ]);
                exit;
            }

            // Continue with CASE 2 (quantity not met) - show cost
            if ($freeShippingQty > 0 && !$excludedFromFree) {

                // Cart quantity < threshold - show cost (multiplied if multiply_by_quantity)
                // If calculated cost is 0, treat as free shipping
                $isFreeCalculated = ($displayCostWithTax == 0);
                $info = [
                    'type' => $isFreeCalculated ? 'free' : 'conditional_free',
                    'reason' => $isFreeCalculated ? 'calculated_cost_zero' : null,
                    'free_from_quantity' => $freeShippingQty,
                    'base_cost' => $baseCost,
                    'cart_quantity' => $cartQuantity
                ];
                // Add additional flags
                if ($separatePackage) {
                    $info['separate_package'] = true;
                }
                if ($multiplyByQty) {
                    $info['multiply_by_quantity'] = true;
                }
                if ($applyThreshold) {
                    $info['apply_threshold'] = true;
                }

                echo json_encode([
                    'success' => true,
                    'lowest_price' => $displayCostWithTax,
                    'formatted_price' => $isFreeCalculated ? 'Za darmo!' : Tools::displayPrice($displayCostWithTax),
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => $isFreeCalculated ? 'zero_cost_quantity' : 'free_shipping_quantity',
                    'is_free_shipping' => $isFreeCalculated,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => $applyThreshold ? $this->getFreeShippingThreshold($id_product) : 0,
                    'shipping_info' => $info
                ]);
                exit;
            }

            // CASE 3: Product is excluded from free shipping (always pays, no matter what)
            if ($excludedFromFree) {
                // If calculated cost is 0, still show "Za darmo!" (even though excluded)
                $isFreeCalculated = ($displayCostWithTax == 0);
                $info = [
                    'excluded' => true,
                    'cart_quantity' => $cartQuantity,
                    'reason' => $isFreeCalculated ? 'calculated_cost_zero' : null
                ];
                if ($separatePackage) {
                    $info['separate_package'] = true;
                }
                if ($multiplyByQty) {
                    $info['multiply_by_quantity'] = true;
                }
                if ($maxQtyPerPackage > 0) {
                    $info['max_qty_per_package'] = $maxQtyPerPackage;
                }

                echo json_encode([
                    'success' => true,
                    'lowest_price' => $displayCostWithTax,
                    'formatted_price' => $isFreeCalculated ? 'Za darmo!' : Tools::displayPrice($displayCostWithTax),
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => $isFreeCalculated ? 'zero_cost_excluded' : 'excluded',
                    'is_free_shipping' => $isFreeCalculated,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => 0,
                    'shipping_info' => $info
                ]);
                exit;
            }

            // CASE 4: Product has separate_package (pays separately, but can participate in threshold)
            if ($separatePackage) {
                // If calculated cost is 0, show "Za darmo!"
                $isFreeCalculated = ($displayCostWithTax == 0);
                $info = [
                    'separate_package' => true,
                    'cart_quantity' => $cartQuantity,
                    'reason' => $isFreeCalculated ? 'calculated_cost_zero' : null
                ];
                if ($multiplyByQty) {
                    $info['multiply_by_quantity'] = true;
                }
                if ($maxQtyPerPackage > 0) {
                    $info['max_qty_per_package'] = $maxQtyPerPackage;
                }
                if ($applyThreshold) {
                    $info['apply_threshold'] = true;
                }

                echo json_encode([
                    'success' => true,
                    'lowest_price' => $displayCostWithTax,
                    'formatted_price' => $isFreeCalculated ? 'Za darmo!' : Tools::displayPrice($displayCostWithTax),
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => $isFreeCalculated ? 'zero_cost_separate' : 'separate_package',
                    'is_free_shipping' => $isFreeCalculated,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => $applyThreshold ? $this->getFreeShippingThreshold($id_product) : 0,
                    'shipping_info' => $info
                ]);
                exit;
            }

            // CASE 5: Product participates in threshold (apply_threshold = 1)
            if ($applyThreshold) {
                $zoneThreshold = $this->getFreeShippingThreshold($id_product);
                // If calculated cost is 0, treat as free shipping
                $isFreeCalculated = ($displayCostWithTax == 0);

                echo json_encode([
                    'success' => true,
                    'lowest_price' => $displayCostWithTax,
                    'formatted_price' => $isFreeCalculated ? 'Za darmo!' : Tools::displayPrice($displayCostWithTax),
                    'carrier_name' => 'Kurier Norwit 01',
                    'calculation_method' => $isFreeCalculated ? 'zero_cost_threshold' : 'threshold_participant',
                    'is_free_shipping' => $isFreeCalculated,
                    'product_id' => $id_product,
                    'free_shipping_threshold' => $zoneThreshold,
                    'shipping_info' => [
                        'type' => $isFreeCalculated ? 'free' : 'threshold',
                        'reason' => $isFreeCalculated ? 'calculated_cost_zero' : null,
                        'participates_in_threshold' => true,
                        'base_cost' => $baseCost,
                        'multiply_by_quantity' => $multiplyByQty,
                        'cart_quantity' => $cartQuantity
                    ]
                ]);
                exit;
            }

            // CASE 6: Product has base_shipping_cost set (standard)
            // If calculated cost is 0, treat as free shipping
            $isFreeCalculated = ($displayCostWithTax == 0);
            echo json_encode([
                'success' => true,
                'lowest_price' => $displayCostWithTax,
                'formatted_price' => $isFreeCalculated ? 'Za darmo!' : Tools::displayPrice($displayCostWithTax),
                'carrier_name' => 'Kurier Norwit 01',
                'calculation_method' => $isFreeCalculated ? 'zero_cost' : 'base_cost',
                'is_free_shipping' => $isFreeCalculated,
                'product_id' => $id_product,
                'free_shipping_threshold' => $this->getFreeShippingThreshold($id_product),
                'shipping_info' => [
                    'type' => $isFreeCalculated ? 'free' : 'standard',
                    'reason' => $isFreeCalculated ? 'calculated_cost_zero' : null,
                    'base_cost' => $baseCost,
                    'multiply_by_quantity' => $multiplyByQty,
                    'max_qty_per_package' => $maxQtyPerPackage,
                    'cart_quantity' => $cartQuantity
                ]
            ]);
            exit;
        }

        // PRIORITY 3: No custom cost - find lowest price from OTHER carriers
        $lowestOtherCarrierPrice = $db->getValue('
            SELECT MIN(d.price)
            FROM '._DB_PREFIX_.'delivery d
            INNER JOIN '._DB_PREFIX_.'carrier c ON d.id_carrier = c.id_carrier
            WHERE c.active = 1
              AND c.deleted = 0
              AND d.id_zone = 1
              AND d.price > 0
        ');

        if ($lowestOtherCarrierPrice && (float)$lowestOtherCarrierPrice > 0) {
            $priceWithVat = (float)$lowestOtherCarrierPrice * 1.23;

            echo json_encode([
                'success' => true,
                'lowest_price' => $priceWithVat,
                'formatted_price' => Tools::displayPrice($priceWithVat),
                'carrier_name' => 'Przesyłka kurierska',
                'calculation_method' => 'lowest_other_carrier',
                'is_free_shipping' => false,
                'product_id' => $id_product,
                'free_shipping_threshold' => $this->getFreeShippingThreshold($id_product)
            ]);
            exit;
        }

        // Fallback: use cart simulation if no other carriers found
        if ($this->context->cart && $this->context->cart->id) {
            $cartTotal = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
            $freeShippingThreshold = $this->getFreeShippingThreshold($id_product);
            $selectedCarrier = $this->context->cart->id_carrier;

            // If carrier is selected, use getTotalShippingCost directly
            if ($selectedCarrier > 0) {
                $totalWithVat = $this->context->cart->getTotalShippingCost(null, true);

                // Free shipping = carrier returns 0 AND cart >= threshold
                $isTrulyFree = ($totalWithVat == 0 && $freeShippingThreshold > 0 && $cartTotal >= $freeShippingThreshold);

                if ($totalWithVat > 0 || $isTrulyFree) {
                    echo json_encode([
                        'success' => true,
                        'lowest_price' => $totalWithVat,
                        'formatted_price' => $isTrulyFree ? 'Za darmo!' : Tools::displayPrice($totalWithVat),
                        'carrier_name' => 'Wysyłka',
                        'calculation_method' => 'selected_carrier',
                        'is_free_shipping' => $isTrulyFree,
                        'product_id' => $id_product,
                        'free_shipping_threshold' => $freeShippingThreshold
                    ]);
                    exit;
                }
                // If carrier returns 0 but not truly free, fall through to simulation
            }

            // Carrier NOT selected or selected returned 0 - simulate all carriers
            try {
                $carriers = $this->context->cart->simulateCarriersOutput(null, true);

                $lowestPrice = null;
                $lowestCarrierName = '';

                foreach ($carriers as $carrier) {
                    $carrierPrice = 0;

                    if (isset($carrier['price_with_tax'])) {
                        $carrierPrice = (float)$carrier['price_with_tax'];
                    } elseif (isset($carrier['total_price_with_tax'])) {
                        $carrierPrice = (float)$carrier['total_price_with_tax'];
                    } elseif (isset($carrier['price'])) {
                        $carrierPrice = (float)$carrier['price'];
                    }

                    // Skip personal pickup options
                    $isPickup = (
                        stripos($carrier['name'], 'sklep') !== false ||
                        stripos($carrier['name'], 'pickup') !== false ||
                        stripos($carrier['name'], 'odbiór') !== false ||
                        stripos($carrier['name'], 'magazyn') !== false ||
                        stripos($carrier['name'], 'self') !== false
                    );

                    if ($isPickup) {
                        continue;
                    }

                    // Carrier returned price 0 - check if cart met the threshold
                    if ($carrierPrice == 0) {
                        if ($freeShippingThreshold > 0 && $cartTotal >= $freeShippingThreshold) {
                            // Cart met threshold - truly free shipping
                            $lowestPrice = 0;
                            $lowestCarrierName = $carrier['name'];
                            break;
                        }
                        // Price 0 but threshold not met - skip (carrier has 0 in native ranges, real price is from external module)
                        continue;
                    }

                    // Track lowest price carrier
                    if ($lowestPrice === null || $carrierPrice < $lowestPrice) {
                        $lowestPrice = $carrierPrice;
                        $lowestCarrierName = $carrier['name'];
                    }
                }

                // Also check getLowestShippingPrice() - simulation may miss native carriers
                // that return 0 without a delivery address but have real prices in the delivery table
                $directLookup = $this->getLowestShippingPrice($id_product);
                $directPrice = ($directLookup && $directLookup['price'] !== null) ? (float)$directLookup['price'] : null;

                if ($lowestPrice !== null || $directPrice !== null) {
                    // Use the lower of simulation result and direct lookup
                    $finalPrice = $lowestPrice;
                    $finalCarrierName = $lowestCarrierName;
                    $calcMethod = 'simulated_carriers';

                    if ($lowestPrice === null || ($directPrice !== null && $directPrice > 0 && $directPrice < $lowestPrice)) {
                        $finalPrice = $directPrice;
                        $finalCarrierName = $directLookup['carrier']['name'] ?? 'Wysyłka';
                        $calcMethod = 'direct_lookup_lower';
                    }

                    $isTrulyFree = ($finalPrice == 0 && $freeShippingThreshold > 0 && $cartTotal >= $freeShippingThreshold);

                    echo json_encode([
                        'success' => true,
                        'lowest_price' => $finalPrice,
                        'formatted_price' => $isTrulyFree ? 'Za darmo!' : Tools::displayPrice($finalPrice),
                        'carrier_name' => $finalCarrierName,
                        'calculation_method' => $calcMethod,
                        'is_free_shipping' => $isTrulyFree,
                        'product_id' => $id_product,
                        'free_shipping_threshold' => $freeShippingThreshold
                    ]);
                    exit;
                }
            } catch (Exception $e) {
            }
        }
        
        // Fallback: if cart doesn't have calculated shipping yet, use old method for single product
        $shippingInfo = $this->getLowestShippingPrice($id_product);

        if ($shippingInfo && $shippingInfo['price'] !== null) {
            echo json_encode([
                'success' => true,
                'lowest_price' => $shippingInfo['price'],
                'formatted_price' => $shippingInfo['formatted_price'],
                'carrier_name' => $shippingInfo['carrier']['name'] ?? 'Unknown',
                'debug_carriers' => $shippingInfo['debug_carriers'] ?? [],
                'product_id' => $shippingInfo['product_id'] ?? null,
                'free_shipping_threshold' => $this->getFreeShippingThreshold($id_product)
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No shipping options available'
            ]);
        }
        exit;
    }

    /**
     * Get lowest carrier cost (without additional_shipping_cost)
     */
    private function getLowestCarrierCost()
    {
        $db = Db::getInstance();

        // Get cheapest active carrier for zone 1
        $query = '
            SELECT MIN(d.price) AS lowest_price
            FROM '._DB_PREFIX_.'carrier c
            INNER JOIN '._DB_PREFIX_.'delivery d ON d.id_carrier = c.id_carrier
            WHERE c.active = 1
              AND c.deleted = 0
              AND d.id_zone = 1
              AND d.price > 0
        ';

        $lowestPrice = $db->getValue($query);

        return $lowestPrice ? (float)$lowestPrice : 25.0; // Fallback 25 PLN
    }

    /**
     * Get shipping cost from carrier wizard (delivery table price ranges)
     * This mirrors the logic in customcarrier module
     */
    private function getWizardShippingCost($context)
    {
        $db = Db::getInstance();

        // Get customcarrier ID
        $customCarrierId = (int) Configuration::get('CUSTOMCARRIER_ID_CARRIER');
        if ($customCarrierId <= 0) {
            return 10.0; // Fallback
        }

        // Get cart total (with tax, without shipping)
        $cartTotal = 0.0;
        if ($context->cart && $context->cart->id) {
            // Cart::BOTH_WITHOUT_SHIPPING = 4
            $cartTotal = (float) $context->cart->getOrderTotal(true, 4);
        }

        // Format cart total for SQL (always use period as decimal separator)
        $cartTotalSql = number_format($cartTotal, 2, '.', '');

        // Find matching price range in delivery table
        // Note: No LIMIT clause - getValue() returns first result anyway
        $query = 'SELECT d.price
            FROM ' . _DB_PREFIX_ . 'delivery d
            INNER JOIN ' . _DB_PREFIX_ . 'range_price rp ON rp.id_range_price = d.id_range_price
            WHERE d.id_carrier = ' . (int) $customCarrierId . '
              AND d.id_zone = 1
              AND rp.delimiter1 <= ' . $cartTotalSql . '
              AND rp.delimiter2 > ' . $cartTotalSql;

        $price = $db->getValue($query);

        if ($price !== false) {
            return (float) $price;
        }

        // No matching range - get lowest price from wizard
        $fallbackQuery = 'SELECT MIN(d.price)
            FROM ' . _DB_PREFIX_ . 'delivery d
            WHERE d.id_carrier = ' . (int) $customCarrierId . '
              AND d.id_zone = 1';

        $fallbackPrice = $db->getValue($fallbackQuery);

        return $fallbackPrice !== false ? (float) $fallbackPrice : 10.0;
    }

    /**
     * AJAX endpoint do pobierania progu darmowej dostawy
     */
    public function getFreeShippingThresholdInfo($id_product = null)
    {
        header('Content-Type: application/json');
        
        $threshold = $this->getFreeShippingThreshold($id_product);
        
        echo json_encode([
            'success' => true,
            'threshold' => $threshold, // Added for JS compatibility
            'free_shipping_threshold' => $threshold,
            'formatted_threshold' => number_format($threshold, 0, ',', ' '),
            'product_id' => $id_product
        ]);
        exit;
    }

    /**
     * Hook called in cart modal
     */
    public function hookDisplayCartModalContent($params)
    {
        if (isset($params['product']['id_product'])) {
            $id_product = (int)$params['product']['id_product'];
            $freeShippingThreshold = $this->getFreeShippingThreshold($id_product);

            // Get customcarrier settings for the product
            $isProductFreeShipping = false;
            $productAlwaysPaysShipping = false;
            $cartHasProductsBlockingFreeShipping = false;

            $customCarrierModule = Module::getInstanceByName('customcarrier');
            if ($customCarrierModule && method_exists($customCarrierModule, 'getProductTransportSettings')) {
                // Check current product
                $ccSettings = $customCarrierModule->getProductTransportSettings($id_product);
                if (!empty($ccSettings)) {
                    // Product has free shipping if NOT excluded AND one of:
                    // 1. free_shipping = 1 (unconditional free shipping)
                    // 2. free_shipping_from_price > 0 AND product price >= threshold
                    $notExcluded = empty($ccSettings['exclude_from_free_shipping']);

                    // Check unconditional free shipping
                    $hasFreeShipping = !empty($ccSettings['free_shipping']);

                    // Check price-based free shipping
                    $freeFromPrice = (float)($ccSettings['free_shipping_from_price'] ?? 0);
                    $hasFreeFromPrice = false;
                    if ($freeFromPrice > 0) {
                        $productPrice = Product::getPriceStatic($id_product, true); // brutto
                        $hasFreeFromPrice = ($productPrice >= $freeFromPrice);
                    }

                    $isProductFreeShipping = $notExcluded && ($hasFreeShipping || $hasFreeFromPrice);

                    // Product always pays: excluded from free shipping OR (separate package without any free shipping rule)
                    // BUT if product qualifies for free shipping (via any rule), it does NOT always pay
                    $baseCostZero = ((float)($ccSettings['base_shipping_cost'] ?? 0) == 0);
                    if ($isProductFreeShipping) {
                        // Product has free shipping - does NOT always pay
                        $productAlwaysPaysShipping = false;
                    } else {
                        // No free shipping - check if always pays (excluded or separate package)
                        $productAlwaysPaysShipping = !empty($ccSettings['exclude_from_free_shipping'])
                            || (!empty($ccSettings['separate_package']) && !$baseCostZero);
                    }
                }

                // Check ALL products in cart for threshold blocking
                // A product blocks threshold free shipping if:
                // 1. Has exclude_from_free_shipping = 1, OR
                // 2. Has separate_package = 1, OR
                // 3. Has custom settings with base_shipping_cost > 0 AND apply_threshold = 0
                if (isset($this->context->cart) && $this->context->cart->id) {
                    $cartProducts = $this->context->cart->getProducts();
                    foreach ($cartProducts as $cartProduct) {
                        $cartProductSettings = $customCarrierModule->getProductTransportSettings((int)$cartProduct['id_product']);
                        if (!empty($cartProductSettings)) {
                            $baseCost = (float)($cartProductSettings['base_shipping_cost'] ?? 0);
                            $applyThreshold = !empty($cartProductSettings['apply_threshold']);
                            $excludedFromFree = !empty($cartProductSettings['exclude_from_free_shipping']);
                            $separatePackage = !empty($cartProductSettings['separate_package']);

                            // Product blocks free shipping threshold if:
                            // - excluded from free shipping, OR
                            // - separate package (always pays), OR
                            // - has base_shipping_cost > 0 AND apply_threshold = NO
                            if ($excludedFromFree || $separatePackage || ($baseCost > 0 && !$applyThreshold)) {
                                $cartHasProductsBlockingFreeShipping = true;
                                break;
                            }
                        }
                    }
                }
            }

            $this->context->smarty->assign([
                'free_shipping_threshold' => $freeShippingThreshold,
                'product_id_for_shipping' => $id_product,
                'is_product_free_shipping' => $isProductFreeShipping,
                'product_always_pays_shipping' => $productAlwaysPaysShipping,
                'cart_has_products_blocking_free_shipping' => $cartHasProductsBlockingFreeShipping,
            ]);
        }

        return '';
    }

    /**
     * AJAX endpoint for getting shipping cost
     */
    public function getShippingCostInfo($id_product)
    {
        header('Content-Type: application/json');
        
        $shippingCost = $this->getShippingCost($id_product);
        
        echo json_encode([
            'success' => true,
            'shipping_cost' => $shippingCost,
            'formatted_cost' => number_format($shippingCost, 2, ',', ' ') . ' zł'
        ]);
        exit;
    }

    /**
     * Get shipping cost for product from PrestaShop delivery system
     */
    private function getShippingCost($id_product)
    {
        try {
            $db = Db::getInstance();
            $product = new Product($id_product);

            if (!$product->id) {
                return $this->getFallbackShippingCost(null);
            }

            // Check customcarrier settings for free shipping rules and base_shipping_cost
            $customCarrierModule = Module::getInstanceByName('customcarrier');
            if ($customCarrierModule && method_exists($customCarrierModule, 'getProductTransportSettings')) {
                $ccSettings = $customCarrierModule->getProductTransportSettings($id_product);
                if (!empty($ccSettings)) {
                    $notExcluded = empty($ccSettings['exclude_from_free_shipping']);

                    // Check unconditional free shipping
                    if ($notExcluded && !empty($ccSettings['free_shipping'])) {
                        return 0.0;
                    }

                    // Check price-based free shipping
                    $freeFromPrice = (float)($ccSettings['free_shipping_from_price'] ?? 0);
                    if ($notExcluded && $freeFromPrice > 0) {
                        $productPrice = Product::getPriceStatic($id_product, true); // brutto
                        if ($productPrice >= $freeFromPrice) {
                            return 0.0;
                        }
                    }

                    // If product has base_shipping_cost, return it (prices are already brutto)
                    // This is the cost for 1 piece/package - modal shows starting price
                    $baseCost = (float)($ccSettings['base_shipping_cost'] ?? 0);
                    if ($baseCost > 0) {
                        return $baseCost;
                    }
                }
            }

            // Get ADDITIONAL shipping costs
            $additionalShippingCost = (float)$product->additional_shipping_cost;
            
            $weight = (float)$product->weight;
            $productPrice = Product::getPriceStatic($id_product, true);
            
            // Simpler query - check if product has assigned carriers
            $productCarriersQuery = 'SELECT d.price AS delivery_price, c.name AS carrier_name
                FROM '._DB_PREFIX_.'carrier c
                INNER JOIN '._DB_PREFIX_.'product_carrier pc ON pc.id_carrier_reference = c.id_reference
                INNER JOIN '._DB_PREFIX_.'delivery d ON d.id_carrier = c.id_carrier
                WHERE pc.id_product = '.(int)$id_product.'
                  AND c.active = 1
                  AND c.deleted = 0
                  AND d.id_zone = 1
                  AND d.price > 0
                ORDER BY d.price ASC';
            
            $result = $db->getValue($productCarriersQuery);
            
            if ($result && $result > 0) {
                // IMPORTANT: Add 23% VAT to database price + additional costs
                $basePrice = (float)$result * 1.23;
                $additionalPrice = $additionalShippingCost * 1.23;
                return $basePrice + $additionalPrice;
            }
            
            // If no assigned carriers, check general carriers
            $generalCarriersQuery = 'SELECT d.price AS delivery_price, c.name AS carrier_name
                FROM '._DB_PREFIX_.'carrier c
                INNER JOIN '._DB_PREFIX_.'delivery d ON d.id_carrier = c.id_carrier
                WHERE c.active = 1
                  AND c.deleted = 0
                  AND d.id_zone = 1
                  AND d.price > 0
                ORDER BY d.price ASC';
            
            $generalResult = $db->getValue($generalCarriersQuery);
            
            if ($generalResult && $generalResult > 0) {
                // IMPORTANT: Add 23% VAT + additional costs
                $basePrice = (float)$generalResult * 1.23;
                $additionalPrice = $additionalShippingCost * 1.23;
                return $basePrice + $additionalPrice;
            }
            
            // Fallback to weight-based system (already includes additional_shipping_cost)
            return $this->getFallbackShippingCost($product);
            
        } catch (Exception $e) {
            return $this->getFallbackShippingCost(new Product($id_product));
        }
    }
    
    /**
     * Get fallback shipping cost based on product weight (old system)
     */
    private function getFallbackShippingCost($product)
    {
        if (!$product || !$product->id) {
            return 30.75; // 25 net * 1.23 VAT
        }
        
        $weight = (float)$product->weight;
        $additionalShippingCost = (float)$product->additional_shipping_cost;
        $additionalShippingCostWithVat = $additionalShippingCost * 1.23;
        
        if ($weight > 5.0) {
            return 151.29 + $additionalShippingCostWithVat; // 123 net * 1.23 VAT - Heavy products
        } elseif ($weight > 2.0) {
            return 37.82 + $additionalShippingCostWithVat; // 30.75 net * 1.23 VAT - Medium products  
        } else {
            return 30.75 + $additionalShippingCostWithVat; // 25 net * 1.23 VAT - Light products
        }
    }
    
    /**
     * Get default shipping cost (simple approach)
     */
    private function getDefaultShippingCost()
    {
        try {
            $db = Db::getInstance();
            
            // Very simple query - get lowest delivery cost
            $defaultQuery = '
                SELECT MIN(price) as min_price
                FROM '._DB_PREFIX_.'delivery 
                WHERE price > 0
            ';
            
            $defaultCost = $db->getValue($defaultQuery);
            
            if ($defaultCost && $defaultCost > 0) {
                return (float)$defaultCost;
            }
        } catch (Exception $e) {
        }
        
        // Ostateczny fallback
        return 25.0;
    }
}