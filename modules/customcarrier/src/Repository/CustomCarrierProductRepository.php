<?php
/**
 * Custom Carrier Product Repository
 *
 * Handles database operations for product transport settings.
 */

declare(strict_types=1);

namespace CustomCarrier\Repository;

use CustomCarrier\Entity\CustomCarrierProduct;
use Doctrine\DBAL\Connection;

class CustomCarrierProductRepository
{
    /** @var Connection */
    private Connection $connection;

    /** @var string */
    private string $dbPrefix;

    /** @var string */
    private string $tableName;

    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->tableName = $dbPrefix . 'customcarrier_product';
    }

    /**
     * Find product settings by product ID
     */
    public function findByProductId(int $idProduct): ?CustomCarrierProduct
    {
        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('*')
            ->from($this->tableName)
            ->where('id_product = :id_product')
            ->setParameter('id_product', $idProduct)
            ->executeQuery()
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        return CustomCarrierProduct::fromArray($result);
    }

    /**
     * Find all product settings
     *
     * @return CustomCarrierProduct[]
     */
    public function findAll(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $results = $qb->select('*')
            ->from($this->tableName)
            ->orderBy('id_product', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $entities = [];
        foreach ($results as $row) {
            $entities[] = CustomCarrierProduct::fromArray($row);
        }

        return $entities;
    }

    /**
     * Find products with free shipping
     *
     * @return CustomCarrierProduct[]
     */
    public function findWithFreeShipping(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $results = $qb->select('*')
            ->from($this->tableName)
            ->where('free_shipping = 1')
            ->executeQuery()
            ->fetchAllAssociative();

        $entities = [];
        foreach ($results as $row) {
            $entities[] = CustomCarrierProduct::fromArray($row);
        }

        return $entities;
    }

    /**
     * Find products requiring separate package
     *
     * @return CustomCarrierProduct[]
     */
    public function findWithSeparatePackage(): array
    {
        $qb = $this->connection->createQueryBuilder();

        $results = $qb->select('*')
            ->from($this->tableName)
            ->where('separate_package = 1')
            ->executeQuery()
            ->fetchAllAssociative();

        $entities = [];
        foreach ($results as $row) {
            $entities[] = CustomCarrierProduct::fromArray($row);
        }

        return $entities;
    }

    /**
     * Save product settings (insert or update)
     */
    public function save(CustomCarrierProduct $entity): bool
    {
        $existing = $this->findByProductId($entity->getIdProduct());

        if ($existing === null) {
            return $this->insert($entity);
        }

        return $this->update($entity);
    }

    /**
     * Insert new product settings
     */
    protected function insert(CustomCarrierProduct $entity): bool
    {
        $data = $entity->toArray();
        $data['date_add'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        try {
            $this->connection->insert($this->tableName, $data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update existing product settings
     */
    protected function update(CustomCarrierProduct $entity): bool
    {
        $data = $entity->toArray();

        try {
            $this->connection->update(
                $this->tableName,
                $data,
                ['id_product' => $entity->getIdProduct()]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete product settings
     */
    public function delete(int $idProduct): bool
    {
        try {
            $this->connection->delete(
                $this->tableName,
                ['id_product' => $idProduct]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get settings for multiple products at once
     *
     * @param int[] $productIds
     * @return array<int, CustomCarrierProduct>
     */
    public function findByProductIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $qb = $this->connection->createQueryBuilder();

        $results = $qb->select('*')
            ->from($this->tableName)
            ->where($qb->expr()->in('id_product', ':product_ids'))
            ->setParameter('product_ids', $productIds, Connection::PARAM_INT_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative();

        $entities = [];
        foreach ($results as $row) {
            $entity = CustomCarrierProduct::fromArray($row);
            $entities[$entity->getIdProduct()] = $entity;
        }

        return $entities;
    }
}
