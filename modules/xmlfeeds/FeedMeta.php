<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class FeedMeta
{
    public function save($feedId)
    {
        $feedMeta = $this->getFeedMeta($feedId);
        $fields = $this->getFeedMetaFields();

        foreach ($fields as $mField) {
            $feedMeta[$feedId][$mField] = Tools::getValue($mField, '');

            if ($mField == 'cron_file_name' || $mField == 'zip_file_name') {
                $feedMeta[$feedId][$mField] = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $feedMeta[$feedId][$mField]);
            }

            if ($mField == 'order_by_column') {
                $feedMeta[$feedId][$mField] = preg_replace('/[^a-zA-Z0-9_ -]/', '', $feedMeta[$feedId][$mField]);
            }
        }

        Configuration::updateValue('BLMOD_XML_FEED_META', htmlspecialchars(json_encode($feedMeta), ENT_QUOTES));
    }

    public function saveFromArray($feedId, $data)
    {
        $feedMeta = $this->getFeedMeta($feedId);
        $fields = $this->getFeedMetaFields();

        foreach ($fields as $mField) {
            $feedMeta[$feedId][$mField] = isset($data[$mField]) ? $data[$mField] : '';
        }

        Configuration::updateValue('BLMOD_XML_FEED_META', htmlspecialchars(json_encode($feedMeta), ENT_QUOTES));
    }

    public function getFeedMeta($feedId, $shopId = null)
    {
        $meta = json_decode(htmlspecialchars_decode(Configuration::get('BLMOD_XML_FEED_META', null, null, $shopId)), true);
        $fields = $this->getFeedMetaFields();

        if (empty($meta[$feedId])) {
            foreach ($fields as $mField) {
                $meta[$feedId][$mField] = '';
            }

            return $meta;
        }

        foreach ($fields as $mField) {
            $meta[$feedId][$mField] = !empty($meta[$feedId][$mField]) ? $meta[$feedId][$mField] : '';
        }

        return $meta;
    }

    public function duplicateValues($feedIdOld, $feedIdNew)
    {
        $feedMeta = $this->getFeedMeta($feedIdOld);
        $feedMeta[$feedIdNew] = [];
        $feedMeta[$feedIdNew] = $feedMeta[$feedIdOld];

        Configuration::updateValue('BLMOD_XML_FEED_META', htmlspecialchars(json_encode($feedMeta), ENT_QUOTES));
    }

    public function getFeedMetaFields()
    {
        return [
            'vivino_bottle_size',
            'vivino_lot_size',
            'shipping_price_mode',
            'spartoo_size',
            'vivino_bottle_size_default',
            'vivino_lot_size_default',
            'last_modified_header',
            'skroutz_analytics_id',
            'edit_price_type',
            'edit_price_value',
            'filter_visibility',
            'product_id_prefix',
            'item_starts_on_a_new_line',
            'is_htmlspecialchars',
            'category_tree_separator',
            'exclude_minimum_order_qty_from',
            'exclude_minimum_order_qty_to',
            'affiliate',
            'price_rounding_type',
            'product_id_with_zero',
            'empty_description',
            'empty_description_text',
            'title_transform',
            'title_length',
            'ean_prefix',
            'reference_prefix',
            'gender_field_category_status',
            'gender_field_category_name',
            'gender_field_category_prime_value',
            'filter_created_before_days',
            'filter_updated_before_days',
            'create_zip_file',
            'zip_file_name',
            'compressor_type',
            'category_tree_type',
            'max_quantity',
            'max_quantity_status',
            'skroutz_variant_size',
            'attribute_id_as_combination_id',
            'unit_price_without_unit',
            'shipping_countries',
            'shipping_countries_status',
            'label_in_stock_text',
            'label_out_of_stock_text',
            'worten_ship_from_country',
            'item_limit',
            'customer_group_id',
            'hide_sale_price',
            'merge_descriptions',
            'mpn_from_attribute',
            'title_attribute_separator',
            'order_by_column',
            'tax_country_id',
            'merge_attributes_parent_id',
            'connected_value_prefix',
            'combination_id_separator',
            'is_use_specific_fixed_price',
            'additional_image_name',
            'cron_file_name',
            'attribute_structure_id',
            'title_editor_text_before',
            'title_editor_text_after',
            'cron_chunks_limit',
            'shipping_min_quantity',
            'label_on_demand_stock_text',
            'is_skip_empty_attribute',
        ];
    }
}
