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

class LegacyCeneoCategoryRepository
{
    private $db;
    private $shop;
    private $db_prefix;
    public $categories;

    const CENEO_CATEGORIES_XML = 'https://developers.ceneo.pl/api/v3/kategorie';

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
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}ceneo_xml_category`(
    			`id_ceneo_category` int(11) NOT NULL,
    			`path` text default NULL,
    			`id_parent` text default NULL,
    			`name` text default NULL,
    			PRIMARY KEY (`id_ceneo_category`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}ceneo_xml_category_mapping`(
    			`id_mapping` int(11) NOT NULL,
    			`categories` text default NULL,
    			PRIMARY KEY (`id_mapping`)
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
			`{$this->db_prefix}ceneo_xml_category`";

        return $this->db->execute($sql);
    }

    protected function xmlToArray($xml, $parents = '')
    {
        foreach ($xml as $element) {
            $this->categories[(string) $element->Id] = $parents . (string) $element->Name;
            if (isset($element->Subcategories)) {
                $this->xmlToArray($element->Subcategories->Category, $parents . (string) $element->Name . '/');
            }
        }
    }

    public function installFixtures()
    {
        $xml = Helper::loadXml(self::CENEO_CATEGORIES_XML);

        if ($xml) {
            $this->xmlToArray($xml);
            if ($this->categories) {
                if (\Db::getInstance()->execute('TRUNCATE `' . $this->db_prefix . 'ceneo_xml_category`')) {
                    foreach ($this->categories as $id => $path) {
                        \Db::getInstance()->insert('ceneo_xml_category', [
                            'id_ceneo_category' => (int) $id,
                            'path' => pSQL((string) $path),
                        ]);
                    }
                } else {
                    return false;
                }

                return true;
            }
        } else {
            return false;
        }

        return false;
    }
}
