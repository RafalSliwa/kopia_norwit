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

namespace CeneoXml\Repository;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CeneoXml\Utils\Helper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use Symfony\Component\Translation\TranslatorInterface;

class CeneoAttributeRepository
{
    private $connection;
    private $db_prefix;
    private $languages;
    private $translator;

    public $attributes;
    const CENEO_CATEGORIES_XML = 'https://developers.ceneo.pl/api/v3/kategorie';

    public function __construct(
        Connection $connection,
        $db_prefix,
        array $languages,
        TranslatorInterface $translator
    ) {
        $this->connection = $connection;
        $this->db_prefix = $db_prefix;
        $this->languages = $languages;
        $this->translator = $translator;
    }

    public function updateFromXml()
    {
        $xml = Helper::loadXml(self::CENEO_CATEGORIES_XML);

        if ($xml) {
            $this->xmlToArray($xml);
            if ($this->attributes) {
                if (\Db::getInstance()->execute('TRUNCATE `' . $this->db_prefix . 'ceneo_xml_attribute`')) {
                    var_dump($this->attributes);
                    exit;
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    protected function xmlToArray($xml, $parents = '')
    {
        foreach ($xml as $element) {
            $this->attributes[(string) $element->Id] = $parents . (string) $element->Name;
            if (isset($element->Subattributes)) {
                $this->xmlToArray($element->Subattributes->Attribute, $parents . (string) $element->Name . '/');
            }
        }
    }

    public function createTables()
    {
        $errors = [];
        $engine = _MYSQL_ENGINE_;
        $this->dropTables();

        $queries = [
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}ceneo_xml_attribute`(
    			`id_ceneo_attribute` int(11) NOT NULL,
    			`path` text default NULL,
    			PRIMARY KEY (`id_ceneo_attribute`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",
        ];

        foreach ($queries as $query) {
            $statement = $this->connection->executeQuery($query);
            if (0 != (int) $statement->errorCode()) {
                $errors[] = [
                    'key' => json_encode($statement->errorInfo()),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }

    public function dropTables()
    {
        $errors = [];
        $tableNames = [
            'ceneo_xml_attribute',
        ];
        foreach ($tableNames as $tableName) {
            $sql = 'DROP TABLE IF EXISTS ' . $this->db_prefix . $tableName;
            $statement = $this->connection->executeQuery($sql);
            if ($statement instanceof Statement && 0 != (int) $statement->errorCode()) {
                $errors[] = [
                    'key' => json_encode($statement->errorInfo()),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }

    private function executeQueryBuilder(QueryBuilder $qb, $errorPrefix = 'SQL error')
    {
        $statement = $qb->execute();
        if ($statement instanceof Statement && !empty($statement->errorInfo())) {
            throw new DatabaseException($errorPrefix . ': ' . var_export($statement->errorInfo(), true));
        }

        return $statement;
    }
}
