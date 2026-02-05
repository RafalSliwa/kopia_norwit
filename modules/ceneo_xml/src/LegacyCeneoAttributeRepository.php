<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace CeneoXml;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoXml\Utils\Helper;

class LegacyCeneoAttributeRepository
{
    private $db;
    private $shop;
    private $db_prefix;
    public $attributes;

    const CENEO_ATTRIBUTES_XML = 'https://developers.ceneo.pl/api/v3/atrybuty';

    /**
     * @param DB $db
     * @param Shop $shop
     */
    public function __construct(\Db $db, \Shop $shop)
    {
        $this->db = $db;
        $this->shop = $shop;
        $this->db_prefix = $db->getPrefix();
    }

    public function createTables()
    {
        $engine = _MYSQL_ENGINE_;
        $success = true;
        $this->dropTables();
        $queries = [
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}ceneo_xml_attribute`(
    			`id_ceneo_category` int(11) NOT NULL,
    			`name` text default NULL,
    			`value` text default NULL,
    			`is_key_attribute` text default NULL
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}ceneo_xml_attribute_mapping`(
    			`id_mapping` int(11) NOT NULL,
    			`attributes` text default NULL,
    			PRIMARY KEY(`id_mapping`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
        ];
        foreach ($queries as $query) {
            $success &= $this->db->execute($query);
        }
        return $success;
    }

    public function dropTables()
    {
        $sql = "DROP TABLE IF EXISTS
			`{$this->db_prefix}ceneo_xml_attribute`";
        return $this->db->execute($sql);
    }

    protected function xmlToArray($xml)
    {
        foreach ($xml as $element) {
            if ($attrs = (array) $element->Subcategories->Attributes) {
                foreach ($attrs['Attribute'] as $a) {
                    $attribute = (array) $a;
                    if (!isset($attribute['Name']) || $attribute['Name'] == null) {
                        continue;
                    }
                    $this->attributes[(string) $element->Id][] = [
                        'name' => $attribute['Name'],
                        'value' => $attribute['Value'],
                        'is_key_attribute' => $attribute['IsKeyAttribute'],
                    ];
                }
            }
            if (isset($element->Subcategories)) {
                $this->xmlToArray($element->Subcategories->Category);
            }
        }
    }

    public function installFixtures(): bool
    {
        $xml = Helper::loadXml(self::CENEO_ATTRIBUTES_XML);

        $res = true;
        if ($xml) {
            $this->xmlToArray($xml);
            if ($this->attributes) {
                if (\Db::getInstance()->execute('TRUNCATE `' . $this->db_prefix . 'ceneo_xml_attribute`')) {
                    foreach ($this->attributes as $id => $array) {
                        foreach ($array as $arr) {
                            \Db::getInstance()->insert('ceneo_xml_attribute', [
                                'id_ceneo_category' => (int) $id,
                                'name' => pSQL($arr['name']),
                                'value' => pSQL($arr['value']),
                                'is_key_attribute' => pSQL($arr['is_key_attribute']),
                            ]);
                        }
                    }
                } else {
                    $res = false;
                }
            }
        } else {
            $res = false;
        }

        return $res;
    }
}
