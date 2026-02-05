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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use Symfony\Component\Translation\TranslatorInterface;

class AttributeMappingRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $db_prefix;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var TranslatorInterface
     */
    private $translator;

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

    public function create(array $data)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->insert($this->db_prefix . 'ds_xml_mapping')
            ->values([
                'id_mapping' => ':id_mapping',
                'id_xml_feed' => ':id_xml_feed',
                'attributes' => ':attributes',
            ])
            ->setParameters([
                'id_mapping' => $data['id_mapping'],
                'id_xml_feed' => $data['id_xml_feed'],
                'attributes' => $data['attributes'],
            ]);

        $this->executeQueryBuilder($qb, 'Mapping error');
        $id_mapping = $this->connection->lastInsertId();

        return $id_mapping;
    }

    public function update($id_mapping, array $data)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->db_prefix . 'ds_xml_mapping', 'xf')
            ->andWhere('xf.id_mapping = :id_mapping')
            ->set('id_xml_feed', ':id_xml_feed')
            ->set('attributes', ':attributes')
            ->setParameters([
                'id_mapping' => $id_mapping,
                'id_xml_feed' => $data['id_xml_feed'],
                'attributes' => $data['attributes'],
            ]);
        $this->executeQueryBuilder($qb, 'Mapping error');
    }

    public function delete($id_mapping)
    {
        $tableNames = [
            'xml_mapping',
        ];

        foreach ($tableNames as $tableName) {
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->delete($this->db_prefix . $tableName)
                ->andWhere('id_mapping = :id_mapping')
                ->setParameter('id_mapping', $id_mapping)
            ;
            $this->executeQueryBuilder($qb, 'Delete error');
        }
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createTables()
    {
        $errors = [];
        $engine = _MYSQL_ENGINE_;
        $this->dropTables();

        $queries = [
            "CREATE TABLE IF NOT EXISTS `{$this->db_prefix}xml_feed`(
    			`id_mapping` int(10) unsigned NOT NULL auto_increment,
    			`id_xml_feed` int(10) unsigned DEFAULT NULL,
    			`attributes` text default NULL,
    			PRIMARY KEY (`id_mapping`)
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

    public function getByIdXmlFeed($id_xml_feed)
    {
        return $this->connection->executeQuery('select * from `' . $this->db_prefix
            . 'ds_xml_mapping` where id_xml_feed = ' . (int) $id_xml_feed)->fetch();
    }

    public function dropTables()
    {
        $errors = [];
        $tableNames = [
            'xml_mapping',
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
