<?php
/**
 * SpecificPrice.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  SpecificPrice
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * SpecificPrice.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class SpecificPrice extends SpecificPriceCore
{
    private static function formatIntInQuery($first_value, $second_value)
    {
        $first_value = (int)$first_value;
        $second_value = (int)$second_value;
        if ($first_value != $second_value) {
            return 'IN ('.$first_value.', '.$second_value.')';
        } else {
            return ' = '.$first_value;
        }
    }

    public static function getSpecificPrice(
        $id_product,
        $id_shop,
        $id_currency,
        $id_country,
        $id_group,
        $quantity,
        $id_product_attribute = null,
        $id_customer = 0,
        $id_cart = 0,
        $real_quantity = 0
    ) {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $key = ((int)$id_product.'-'.(int)$id_shop.'-'.(int)$id_currency.'-'.
            (int)$id_country.'-'.(int)$id_group.'-'.(int)$quantity.'-'.
            (int)$id_product_attribute.'-'.(int)$id_cart.'-'.(int)$id_customer.'-'.(int)$real_quantity);
        if (!array_key_exists($key, SpecificPrice::$_specificPriceCache)) {
            $query_extra = self::computeExtraConditions($id_product, $id_product_attribute, $id_customer, $id_cart);
            $query =
                'SELECT *, '.SpecificPrice::_getScoreQuery(
                    $id_product,
                    $id_shop,
                    $id_currency,
                    $id_country,
                    $id_group,
                    $id_customer
                ).'
                FROM `'._DB_PREFIX_.'specific_price`
                WHERE
                `id_shop` '.self::formatIntInQuery(0, $id_shop).' AND
                `id_currency` '.self::formatIntInQuery(0, $id_currency).' AND
                `id_country` '.self::formatIntInQuery(0, $id_country).' AND
                `id_group` '.self::formatIntInQuery(0, $id_group).' '.$query_extra.'
                AND IF(`from_quantity` > 1, `from_quantity`, 0) <= ';

            $query .= (Configuration::get('PS_QTY_DISCOUNT_ON_COMBINATION') || !$id_cart || !$real_quantity) ?
                (int)$quantity : max(1, (int)$real_quantity);
            $query .= ' ORDER BY `id_product_attribute` DESC, `from_quantity` 
                DESC, `id_specific_price_rule` ASC, `score` DESC, `to` DESC, `from` DESC';

            SpecificPrice::$_specificPriceCache[$key] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        }
        return SpecificPrice::$_specificPriceCache[$key];
    }

    /**
     * Remove or add a field value to a query if values are present in the database (cache friendly)
     *
     * @param string $field_name
     * @param int $field_value
     * @param int $threshold
     * @return string
     * @throws PrestaShopDatabaseException
     */
    protected static function filterOutField($field_name, $field_value, $threshold = 1000)
    {
        $query_extra = 'AND `'.$field_name.'` = 0 ';
        if ($field_value == 0 || array_key_exists($field_name, self::$_no_specific_values)) {
            return $query_extra;
        }
        $key_cache     = __FUNCTION__.'-'.$field_name.'-'.$threshold;
        $specific_list = array();
        if (!array_key_exists($key_cache, SpecificPrice::$_filterOutCache)) {
            $query_count = 'SELECT COUNT(DISTINCT `'.$field_name.'`) 
                FROM `'._DB_PREFIX_.'specific_price`
                WHERE `'.$field_name.'` != 0';
            $specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_count);
            if ($specific_count == 0) {
                self::$_no_specific_values[$field_name] = true;

                return $query_extra;
            }
            if ($specific_count < $threshold) {
                $query = 'SELECT DISTINCT `'.$field_name.'` 
                    FROM `'._DB_PREFIX_.'specific_price`
                    WHERE `'.$field_name .'` != 0';
                $tmp_specific_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                foreach ($tmp_specific_list as $value) {
                    $specific_list[] = $value[$field_name];
                }
            }
            SpecificPrice::$_filterOutCache[$key_cache] = $specific_list;
        } else {
            $specific_list = SpecificPrice::$_filterOutCache[$key_cache];
        }

        // $specific_list is empty if the threshold is reached
        if (empty($specific_list) || in_array($field_value, $specific_list)) {
            $query_extra = 'AND `'.$field_name.'` '.self::formatIntInQuery(0, $field_value).' ';
        }

        return $query_extra;
    }

    /**
     * Remove or add useless fields value depending on the values in the database (cache friendly)
     *
     * @param int|null $id_product
     * @param int|null $id_product_attribute
     * @param int|null $id_cart
     * @param string|null $beginning
     * @param string|null $ending
     * @return string
     */
    protected static function computeExtraConditions(
        $id_product,
        $id_product_attribute,
        $id_customer,
        $id_cart,
        $beginning = null,
        $ending = null
    ) {
        $first_date = date('Y-m-d 00:00:00');
        $last_date = date('Y-m-d 23:59:59');
        $now = date('Y-m-d H:i:00');
        if ($beginning === null) {
            $beginning = $now;
        }
        if ($ending === null) {
            $ending = $now;
        }
        $id_customer = (int)$id_customer;
        $id_cart = (int)$id_cart;

        $query_extra = '';

        if ($id_product !== null) {
            $query_extra .= self::filterOutField('id_product', $id_product);
        }

        if ($id_customer !== null) {
            $query_extra .= self::filterOutField('id_customer', $id_customer);
        }

        if ($id_product_attribute !== null) {
            $query_extra .= self::filterOutField('id_product_attribute', $id_product_attribute);
        }

        if ($id_cart !== null) {
            $query_extra .= self::filterOutField('id_cart', $id_cart);
        }

        if ($ending == $now && $beginning == $now) {
            $key = __FUNCTION__.'-'.$first_date.'-'.$last_date;
            if (!array_key_exists($key, SpecificPrice::$_filterOutCache)) {
                $query_from_count = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` 
                WHERE `from` BETWEEN \'' .$first_date.'\' AND \''.$last_date.'\'';
                $from_specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_from_count);

                $query_to_count = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` 
                    WHERE `to` BETWEEN \''.$first_date.'\' AND \''.$last_date.'\'';

                $to_specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_to_count);
                SpecificPrice::$_filterOutCache[$key] = array($from_specific_count, $to_specific_count);
            } else {
                list($from_specific_count, $to_specific_count) = SpecificPrice::$_filterOutCache[$key];
            }
        } else {
            $from_specific_count = $to_specific_count = 1;
        }

        if (!$from_specific_count && !$to_specific_count) {
            $ending = $beginning = $first_date;
        }

        $query_extra .= ' AND (`from` = \'0000-00-00 00:00:00\' OR \''.$beginning.'\' >= `from`)'
            .' AND (`to` = \'0000-00-00 00:00:00\' OR \''.$ending.'\' <= `to`)';

        return $query_extra;
    }
}
