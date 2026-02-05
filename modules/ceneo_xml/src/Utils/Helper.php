<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace CeneoXml\Utils;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Helper
{
    public static function loadXml($url)
    {
        set_time_limit(80);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 80);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);

        $result = curl_exec($curl);
        if ($result === false) {
            $error = curl_error($curl);
            curl_close($curl);
            return false;
        }

        curl_close($curl);

        if (!empty($result)) {
            return simplexml_load_string($result);
        } else {
            return false;
        }
    }

    public static function getAvailabilitiesLabels($stock_management, $module)
    {
        $availabilities_labels = [
            ['key' => 1, 'name' => html_entity_decode($module->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "check availability".'))],
            ['key' => 2, 'name' => html_entity_decode($module->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "available up to 3 days" (the store will send the product up to 3 days).'))],
            ['key' => 3, 'name' => html_entity_decode($module->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "available up to a week" (the store will send the product within a week).'))],
            ['key' => 4, 'name' => html_entity_decode($module->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "available up to 14 days" (the store will send the product up to 14 days).'))],
            ['key' => 5, 'name' => html_entity_decode($module->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "on order".'))],
            ['key' => 6, 'name' => html_entity_decode($module->l('If the product is in stock then set availability: "available" (the store will send the product within 24 hours), if not then: "on pre-order".'))],
            ['key' => 7, 'name' => html_entity_decode($module->l('Set the availability for all products to: "available" (the store will ship the product within 24 hours).'))],
            ['key' => 8, 'name' => html_entity_decode($module->l('Set the availability for all products to: "available up to 3 days" (the store will ship the product up to 3 days).'))],
            ['key' => 9, 'name' => html_entity_decode($module->l('Set the availability for all products to: "available up to a week" (the store will ship the product within a week).'))],
            ['key' => 10, 'name' => html_entity_decode($module->l('Set the availability for all products to: "available up to 14 days" (the store will ship the product up to 14 days).'))],
            ['key' => 11, 'name' => html_entity_decode($module->l('Set the availability for all products to: "on order".'))],
            ['key' => 12, 'name' => html_entity_decode($module->l('Set the availability for all products to: "on pre-order".'))],
            ['key' => 13, 'name' => html_entity_decode($module->l('Set the availability for all products to: "check availability".'))],
        ];
        if (!$stock_management) {
            $availabilities_labels = array_slice($availabilities_labels, 7);
        }

        return array_values($availabilities_labels);
    }

    public static function getSingleAvailabilitiesLabels($stock_management, $module)
    {
        $availabilities_labels_single = [
            ['key' => -1, 'name' => $module->l('Default')],
            ['key' => 1, 'name' => html_entity_decode($module->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "check availability".'))],
            ['key' => 2, 'name' => html_entity_decode($module->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "available up to 3 days" (the store will send the product up to 3 days).'))],
            ['key' => 3, 'name' => html_entity_decode($module->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "available up to a week" (the store will send the product within a week).'))],
            ['key' => 4, 'name' => html_entity_decode($module->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "available up to 14 days" (the store will send the product up to 14 days).'))],
            ['key' => 5, 'name' => html_entity_decode($module->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "on order".'))],
            ['key' => 6, 'name' => html_entity_decode($module->l('If the product is in stock then set the availability: "available" (the store will send the product within 24 hours), if not then: "on pre-order".'))],
            ['key' => 7, 'name' => html_entity_decode($module->l('Set availability to: "available" (the store will ship the product within 24 hours).'))],
            ['key' => 8, 'name' => html_entity_decode($module->l('Set availability to: "available up to 3 days" (the store will ship the product up to 3 days).'))],
            ['key' => 9, 'name' => html_entity_decode($module->l('Set availability to: "available up to a week" (the store will ship the product within a week).'))],
            ['key' => 10, 'name' => html_entity_decode($module->l('Set availability to: "available up to 14 days" (the store will ship the product up to 14 days).'))],
            ['key' => 11, 'name' => html_entity_decode($module->l('Set availability to: "on order".'))],
            ['key' => 12, 'name' => html_entity_decode($module->l('Set availability to: "on pre-sale".'))],
            ['key' => 13, 'name' => html_entity_decode($module->l('Set availability to: "check availability".'))],
        ];

        if (!$stock_management) {
            $availabilities_labels_single = array_slice($availabilities_labels_single, 7);
        }

        return array_values($availabilities_labels_single);
    }

    public static function transformArray($inputArray): array
    {
        $outputArray = [];
        foreach ($inputArray as $item) {
            $key = $item['name'];
            $value = $item['key'];
            $outputArray[$key] = $value;
        }

        return $outputArray;
    }
}
