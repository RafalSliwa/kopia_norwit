<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace FacebookProductAd\Dao;

if (!defined('_PS_VERSION_')) {
    exit;
}
class customLabelDao
{
    /**
     * Get productBestSales with set parameters for one TAG
     *
     * @param string $new_date_from
     *
     * @return mixed of ids
     */
    public static function getNewProducts($new_date_from)
    {
        $query = new \DbQuery();
        $query->select('DISTINCT(p.id_product)');
        $query->from('product', 'p');
        $query->where('p.date_add >= "' .  \pSQL($new_date_from) . '"');

        return \Db::getInstance()->ExecuteS($query);
    }

    /**
     * Get productBestSales with set parameters for one TAG
     *
     * @param string $unit
     * @param float $amount
     * @param string $start_date
     * @param string $end_date
     *
     * @return mixed of ids
     */
    public static function getProductBestSales($unit, $amount, $start_date = null, $end_date = null)
    {
        $query = new \DbQuery();

        if (!empty($unit)) {
            $query->select('DISTINCT(pod.product_id), SUM(pod.product_quantity) as qty');
            $query->from('order_detail', 'pod');
            $query->leftJoin('orders', 'po', 'po.id_order = pod.id_order');

            if (!empty($start_date)) {
                $query->where('po.date_add >= "' . \pSQL($start_date) . '"');
            }

            if (!empty($end_date)) {
                $query->where('po.date_add <= "' . \pSQL($end_date) . '"');
            }

            $query->groupBy('pod.product_id HAVING qty >= ' . (int) $amount);
        } elseif (!empty($amount)) {
            $query->select('pod.product_id, SUM(pod.total_price_tax_incl) as total_sale_amount');
            $query->from('order_detail', 'pod');
            $query->leftJoin('orders', 'po', 'po.id_order = pod.id_order');

            if (!empty($start_date)) {
                $query->where('po.date_add >= "' . \pSQL($start_date) . '"');
            }
            if (!empty($end_date)) {
                $query->where('po.date_add <= "' . \pSQL($end_date) . '"');
            }
            $query->groupBy('pod.product_id HAVING SUM(pod.total_price_tax_incl) >= ' . (float) $amount);
            $query->orderBy('total_sale_amount DESC');
        }

        return \Db::getInstance()->ExecuteS($query);
    }

    /**
     * returns Google tags for XML
     *
     * @param int $id_tag
     * @param array $filter
     */
    public static function getCustomLabelProductIds($id_tag, $filter)
    {
        $query = new \DbQuery();
        $query->select($filter['sFieldSelect']);
        $query->from($filter['sPopulateTable']);
        $query->where('id_tag = ' . (int) $id_tag);

        $product_ids = \Db::getInstance()->ExecuteS($query);

        if ($filter['bUsePsTable'] == 1 && !empty($product_ids)) {
            foreach ($product_ids as $filter_id) {
                $sub_query = new \DbQuery();
                $sub_query->select('id_product');
                $sub_query->from($filter['sPsTable']);
                $sub_query->where(\pSQL($filter['sPsTableWhere']) . '=' . (int) $filter_id[$filter['sFieldSelect']]);
                $product_ids = \Db::getInstance()->ExecuteS($sub_query);
            }
        }

        foreach ($product_ids as $aProductId) {
            array_push($product_ids, $aProductId['id_product']);
        }

        return $product_ids;
    }

    /**
     * Get getPriceRangeProduct with set parameters for one TAG
     *
     * @param string $price_min
     * @param string $price_max
     *
     * @return mixed of ids
     */
    public static function getPriceRangeProduct($price_min, $price_max)
    {
        $query = new \DbQuery();
        $query->select('DISTINCT(p.id_product)');
        $query->from('product', 'p');
        $query->where('p.price >= "' . \pSQL($price_min) . '"');
        $query->where('p.price <="' . \pSQL($price_max) . '"');

        return \Db::getInstance()->ExecuteS($query);
    }

    /**
     * returns Google tags for XML
     *
     * @param int $id_product
     * @param array $categories
     * @param int $id_manufacturer
     * @param int $id_supplier
     * @param int $id_lang
     *
     * @return mixed
     */
    public static function getTagsForXml($id_product, $categories, $id_manufacturer, $id_supplier, $id_lang)
    {
        try {
            $in_data = implode(',', array_map('intval', $categories));

            $global = new \DbQuery();
            $global->select('DISTINCT(gt.id_tag), fvl.value as name, gt.type, gt.custom_label_set_postion');
            $global->from('fpa_tags', 'gt');
            $global->leftJoin('fpa_tags_dynamic_features', 'gtdf', 'gt.id_tag = gtdf.id_tag');
            $global->leftJoin('feature_lang', 'fl', 'gtdf.id_feature = fl.id_feature');
            $global->leftJoin('feature_product', 'fp', 'fl.id_feature = fp.id_feature');
            $global->leftJoin('feature_value_lang', 'fvl', 'fp.id_feature_value = fvl.id_feature_value');
            $global->where('fp.id_product = ' . (int) $id_product);
            $global->where('fl.id_lang = ' . (int) $id_lang);
            $global->where('fvl.id_lang = ' . (int) $id_lang);
            $global->where('gt.active = 1');
            $global->orderBy('gt.custom_label_set_postion ASC');

            $productTag = new \DbQuery();
            $productTag->select('DISTINCT(gt.id_tag),gt.name, gt.type, gt.custom_label_set_postion');
            $productTag->from('fpa_tags', 'gt');
            $productTag->leftJoin('fpa_tags_products', 'gtp', 'gt.id_tag = gtp.id_tag');
            $productTag->where('gtp.id_product = ' . (int) $id_product);
            $productTag->where('gt.active = 1');
            $productTag->orderBy('gt.custom_label_set_postion ASC');

            $dynCategoriesTag = new \DbQuery();
            $dynCategoriesTag->select('DISTINCT(gt.id_tag),cl.name as name, gt.type, gt.custom_label_set_postion');
            $dynCategoriesTag->from('fpa_tags', 'gt');
            $dynCategoriesTag->leftJoin('fpa_tags_dynamic_categories', 'gtdc', 'gt.id_tag = gtdc.id_tag');
            $dynCategoriesTag->leftJoin('category_lang', 'cl', 'cl.id_category = gtdc.id_category');
            $dynCategoriesTag->where('cl.id_category IN (' . \pSQL($in_data) . ')');
            $dynCategoriesTag->where('gt.active = 1');
            $dynCategoriesTag->orderBy('gt.custom_label_set_postion ASC');

            $dynBestSales = new \DbQuery();
            $dynBestSales->select('distinct(gt.id_tag),gt.name, gt.type, gt.custom_label_set_postion');
            $dynBestSales->from('fpa_tags', 'gt');
            $dynBestSales->leftJoin('fpa_tags_dynamic_best_sale', 'gtdbs', 'gt.id_tag = gtdbs.id_tag');
            $dynBestSales->where('gtdbs.id_product = ' . (int) $id_product);
            $dynBestSales->where('gt.active = 1');
            $dynBestSales->orderBy('gt.custom_label_set_postion ASC');

            $dynamicNewProduct = new \DbQuery();
            $dynamicNewProduct->select('distinct(gt.id_tag),gt.name, gt.type, gt.custom_label_set_postion');
            $dynamicNewProduct->from('fpa_tags', 'gt');
            $dynamicNewProduct->leftJoin('fpa_tags_dynamic_new_product', 'gtdnp', 'gt.id_tag = gtdnp.id_tag');
            $dynamicNewProduct->where('gtdnp.id_product = ' . (int) $id_product);
            $dynamicNewProduct->where('gt.active = 1');
            $dynamicNewProduct->orderBy('gt.custom_label_set_postion ASC');

            $manualCategoriesTag = new \DbQuery();
            $manualCategoriesTag->select('distinct(gt.id_tag), gt.name, gt.type, gt.custom_label_set_postion');
            $manualCategoriesTag->from('fpa_tags', 'gt');
            $manualCategoriesTag->leftJoin('fpa_tags_cats', 'gtc', 'gt.id_tag = gtc.id_tag');
            $manualCategoriesTag->where('gtc.id_category IN (' . \pSQL($in_data) . ')');
            $manualCategoriesTag->where('gt.active = 1');
            $manualCategoriesTag->orderBy('gt.custom_label_set_postion ASC');

            $dynamicPriceRangeTag = new \DbQuery();
            $dynamicPriceRangeTag->select('distinct(gt.id_tag),gt.name, gt.type, gt.custom_label_set_postion');
            $dynamicPriceRangeTag->from('fpa_tags', 'gt');
            $dynamicPriceRangeTag->leftJoin('fpa_tags_price_range', 'gtdpr', 'gt.id_tag = gtdpr.id_tag');
            $dynamicPriceRangeTag->where('gtdpr.id_product = ' . (int) $id_product);
            $dynamicPriceRangeTag->where('gt.active = 1');
            $dynamicPriceRangeTag->orderBy('gt.custom_label_set_postion ASC');

            $manualBrandTag = new \DbQuery();
            $manualBrandTag->select('distinct(gt.id_tag), gt.name, gt.type, gt.custom_label_set_postion');
            $manualBrandTag->from('fpa_tags', 'gt');
            $manualBrandTag->leftJoin('fpa_tags_brands', 'gtb', 'gt.id_tag = gtb.id_tag');
            $manualBrandTag->where('gtb.id_brand = ' . (int) $id_manufacturer);
            $manualBrandTag->where('gt.active = 1');
            $manualBrandTag->orderBy('gt.custom_label_set_postion ASC');

            $dynamicLastOrderedTag = new \DbQuery();
            $dynamicLastOrderedTag->select('distinct(gt.id_tag),gt.name, gt.type, gt.custom_label_set_postion');
            $dynamicLastOrderedTag->from('fpa_tags', 'gt');
            $dynamicLastOrderedTag->leftJoin('fpa_tags_dynamic_last_product_ordered', 'gtdblo', 'gt.id_tag = gtdblo.id_tag');
            $dynamicLastOrderedTag->where('gtdblo.id_product = ' . (int) $id_product);
            $dynamicLastOrderedTag->where('gt.active = 1');
            $manualBrandTag->orderBy('gt.custom_label_set_postion ASC');

            $dynamicPromotionTag = new \DbQuery();
            $dynamicPromotionTag->select('distinct(gt.id_tag),gt.name, gt.type, gt.custom_label_set_postion');
            $dynamicPromotionTag->from('fpa_tags', 'gt');
            $dynamicPromotionTag->leftJoin('fpa_tags_dynamic_promotion', 'gtdp', 'gt.id_tag = gtdp.id_tag');
            $dynamicPromotionTag->where('gtdp.id_product = ' . (int) $id_product);
            $dynamicPromotionTag->where('gt.active = 1');
            $dynamicPromotionTag->orderBy('gt.custom_label_set_postion ASC');

            $manualSupplierTag = new \DbQuery();
            $manualSupplierTag->select('distinct(gt.id_tag), gt.name, gt.type, gt.custom_label_set_postion');
            $manualSupplierTag->from('fpa_tags', 'gt');
            $manualSupplierTag->leftJoin('fpa_tags_suppliers', 'gts', 'gt.id_tag = gts.id_tag');
            $manualSupplierTag->where('gts.id_supplier IN (SELECT distinct(id_supplier) FROM `' . _DB_PREFIX_ . 'product_supplier` WHERE id_product = ' . (int) $id_product . ')');
            $manualSupplierTag->where('gt.active = 1');
            $manualSupplierTag->orderBy('gt.custom_label_set_postion ASC');

            $globalData = \Db::getInstance()->ExecuteS($global);
            $productTagData = \Db::getInstance()->ExecuteS($productTag);
            $dynCategoriesTagData = \Db::getInstance()->ExecuteS($dynCategoriesTag);
            $dynBestSales = \Db::getInstance()->ExecuteS($dynBestSales);
            $dynamicNewProductData = \Db::getInstance()->ExecuteS($dynamicNewProduct);
            $manualCategoriesTagData = \Db::getInstance()->ExecuteS($manualCategoriesTag);
            $dynamicPriceRangeTagData = \Db::getInstance()->ExecuteS($dynamicPriceRangeTag);
            $manualBrandTagData = \Db::getInstance()->ExecuteS($manualBrandTag);
            $dynamicLastOrderedTagData = \Db::getInstance()->ExecuteS($dynamicLastOrderedTag);
            $dynamicPromotionTagData = \Db::getInstance()->ExecuteS($dynamicPromotionTag);
            $manualSupplierTagData = \Db::getInstance()->ExecuteS($manualSupplierTag);

            $database_tag_values = array_merge($globalData, $productTagData, $dynCategoriesTagData, $dynBestSales, $dynamicNewProductData, $manualCategoriesTagData, $dynamicPriceRangeTagData, $manualBrandTagData, $dynamicLastOrderedTagData, $dynamicPromotionTagData, $manualSupplierTagData);

            $sortedLabels = [];
            $labels = ['custom_label' => []];

            // Fill out each case to have the custom label on the good position and make management for
            // https://support.google.com/google-ads/answer/6275295?hl=en
            foreach ($database_tag_values as $key => $label) {
                if ($label['custom_label_set_postion'] == 'custom_label_0') {
                    $sortedLabels[0][] = $label;
                }

                if ($label['custom_label_set_postion'] == 'custom_label_1') {
                    $sortedLabels[1][] = $label;
                }

                if ($label['custom_label_set_postion'] == 'custom_label_2') {
                    $sortedLabels[2][] = $label;
                }

                if ($label['custom_label_set_postion'] == 'custom_label_3') {
                    $sortedLabels[3][] = $label;
                }

                if ($label['custom_label_set_postion'] == 'custom_label_4') {
                    $sortedLabels[4][] = $label;
                }
            }

            // sort on key to get the value like custom_label_0 custom_label_1 etc
            ksort($sortedLabels);

            if (!empty($sortedLabels) && is_array($sortedLabels)) {
                foreach ($sortedLabels as $row) {
                    // Only get the first value encountered to prevent multiple same custom_label ids
                    $labels['custom_label'][] = ['value' => $row[0]['name'], 'position' => $row[0]['custom_label_set_postion']];
                }
            }

            return $labels;
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 2, $e->getCode(), null, null, true);
        }
    }
}
