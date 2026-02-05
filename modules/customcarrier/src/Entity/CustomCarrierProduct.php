<?php
/**
 * Custom Carrier Product Entity
 *
 * Represents transport settings for a product.
 */

declare(strict_types=1);

namespace CustomCarrier\Entity;

class CustomCarrierProduct
{
    /** @var int|null */
    private ?int $id = null;

    /** @var int */
    private int $idProduct;

    /** @var bool */
    private bool $freeShipping = false;

    /** @var float */
    private float $baseShippingCost = 0.0;

    /** @var bool */
    private bool $multiplyByQuantity = false;

    /** @var int */
    private int $freeShippingQuantity = 0;

    /** @var bool */
    private bool $applyThreshold = false;

    /** @var bool */
    private bool $separatePackage = false;

    /** @var \DateTimeInterface */
    private \DateTimeInterface $dateAdd;

    /** @var \DateTimeInterface */
    private \DateTimeInterface $dateUpd;

    public function __construct()
    {
        $this->dateAdd = new \DateTimeImmutable();
        $this->dateUpd = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIdProduct(): int
    {
        return $this->idProduct;
    }

    public function setIdProduct(int $idProduct): self
    {
        $this->idProduct = $idProduct;
        return $this;
    }

    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    public function setFreeShipping(bool $freeShipping): self
    {
        $this->freeShipping = $freeShipping;
        return $this;
    }

    public function getBaseShippingCost(): float
    {
        return $this->baseShippingCost;
    }

    public function setBaseShippingCost(float $baseShippingCost): self
    {
        $this->baseShippingCost = $baseShippingCost;
        return $this;
    }

    public function isMultiplyByQuantity(): bool
    {
        return $this->multiplyByQuantity;
    }

    public function setMultiplyByQuantity(bool $multiplyByQuantity): self
    {
        $this->multiplyByQuantity = $multiplyByQuantity;
        return $this;
    }

    public function getFreeShippingQuantity(): int
    {
        return $this->freeShippingQuantity;
    }

    public function setFreeShippingQuantity(int $freeShippingQuantity): self
    {
        $this->freeShippingQuantity = $freeShippingQuantity;
        return $this;
    }

    public function isApplyThreshold(): bool
    {
        return $this->applyThreshold;
    }

    public function setApplyThreshold(bool $applyThreshold): self
    {
        $this->applyThreshold = $applyThreshold;
        return $this;
    }

    public function isSeparatePackage(): bool
    {
        return $this->separatePackage;
    }

    public function setSeparatePackage(bool $separatePackage): self
    {
        $this->separatePackage = $separatePackage;
        return $this;
    }

    public function getDateAdd(): \DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;
        return $this;
    }

    public function getDateUpd(): \DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;
        return $this;
    }

    /**
     * Create entity from database row
     */
    public static function fromArray(array $data): self
    {
        $entity = new self();

        if (isset($data['id_customcarrier_product'])) {
            $entity->setId((int) $data['id_customcarrier_product']);
        }

        $entity->setIdProduct((int) ($data['id_product'] ?? 0));
        $entity->setFreeShipping((bool) ($data['free_shipping'] ?? false));
        $entity->setBaseShippingCost((float) ($data['base_shipping_cost'] ?? 0.0));
        $entity->setMultiplyByQuantity((bool) ($data['multiply_by_quantity'] ?? false));
        $entity->setFreeShippingQuantity((int) ($data['free_shipping_quantity'] ?? 0));
        $entity->setApplyThreshold((bool) ($data['apply_threshold'] ?? false));
        $entity->setSeparatePackage((bool) ($data['separate_package'] ?? false));

        if (!empty($data['date_add'])) {
            $entity->setDateAdd(new \DateTimeImmutable($data['date_add']));
        }

        if (!empty($data['date_upd'])) {
            $entity->setDateUpd(new \DateTimeImmutable($data['date_upd']));
        }

        return $entity;
    }

    /**
     * Convert entity to array for database
     */
    public function toArray(): array
    {
        return [
            'id_product' => $this->idProduct,
            'free_shipping' => $this->freeShipping ? 1 : 0,
            'base_shipping_cost' => $this->baseShippingCost,
            'multiply_by_quantity' => $this->multiplyByQuantity ? 1 : 0,
            'free_shipping_quantity' => $this->freeShippingQuantity,
            'apply_threshold' => $this->applyThreshold ? 1 : 0,
            'separate_package' => $this->separatePackage ? 1 : 0,
            'date_upd' => $this->dateUpd->format('Y-m-d H:i:s'),
        ];
    }
}
