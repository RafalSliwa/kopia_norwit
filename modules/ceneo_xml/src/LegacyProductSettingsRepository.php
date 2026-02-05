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

class LegacyProductSettingsRepository
{
    private $db;
    private $shop;
    private $db_prefix;

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

    public function getTableName(): string
    {
        return $this->db_prefix . 'ceneo_xml_product_settings';
    }

    public function createTables()
    {
        $engine = _MYSQL_ENGINE_;
        $this->dropTables();
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}ceneo_xml_product_settings`(
            `id_exclude_product` int(10) unsigned NOT NULL auto_increment,
            `id_product` int(10) unsigned DEFAULT NULL,
            `id_product_attribute` int(10),
            `exclude` int(10) unsigned NOT NULL,
            `avail` int(10) unsigned NOT NULL,
            `basket` int(10) unsigned NULL,
            PRIMARY KEY (`id_exclude_product`)
        ) ENGINE=$engine DEFAULT CHARSET=utf8";

        return $this->db->execute($sql);
    }

    public function dropTables()
    {
        $sql = "DROP TABLE IF EXISTS `{$this->getTableName()}`";

        return $this->db->execute($sql);
    }

    public function deleteByIdProduct($id_product, $id_product_attribute = null)
    {
        $result = \Db::getInstance()->execute('DELETE from `' . $this->getTableName() . '` 
        where id_product = ' . (int) $id_product . ' and id_product_attribute = "'
            . (int) $id_product_attribute . '"');

        return $result ? 1 : 0;
    }

    public function setExcludeByIdProduct($id_product, $id_product_attribute = null, $exclude = 0)
    {
        return \Db::getInstance()->execute('UPDATE `' . $this->getTableName() . '` 
        set exclude = ' . (int) $exclude . ' where id_product_attribute = "'
            . (int) $id_product_attribute . '" and id_product = ' . (int) $id_product);
    }

    public function setBasketByIdProduct($id_product, $basket)
    {
        return \Db::getInstance()->execute('UPDATE `' . $this->getTableName() . '` 
        set basket = ' . $basket . ' where id_product = ' . $id_product);
    }

    public function setAvailByIdProduct($id_product, $avail)
    {
        return \Db::getInstance()->execute('UPDATE `' . $this->getTableName() . '` 
        set avail = ' . (int) $avail . ' where id_product = ' . (int) $id_product);
    }

    public function addByIdProduct($id_product, $id_product_attribute = null)
    {
        return \Db::getInstance()->execute('INSERT INTO `' . $this->getTableName() . '` 
        VALUES(null, ' . (int) $id_product . ', "' . (int) $id_product_attribute . '", 0, "'
            . pSQL(\Configuration::get('CENEO_XML_AVAIL')) . '", "' . pSQL(\Configuration::get('CENEO_XML_BASKET')) . '")');
    }

    public function getByIdProduct($id_product, $id_product_attribute = null)
    {
        $result = \Db::getInstance()->getRow('select * from `' . $this->getTableName() . '` 
        where id_product = ' . (int) $id_product . ' and id_product_attribute = "'
            . (int) $id_product_attribute . '"');
        if (!$result) {
            $this->addByIdProduct($id_product, $id_product_attribute);

            return \Db::getInstance()->getRow('select * from `' . $this->getTableName() . '` 
            where id_product = ' . (int) $id_product . ' and id_product_attribute = "'
                . (int) $id_product_attribute . '"');
        }

        return $result;
    }
}
