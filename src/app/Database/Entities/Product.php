<?php 

namespace App\Database\Entities;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

class Product
{
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_LOCATION_ID = 'locationId';
    public const PROPERTY_PRICE = 'price';
    public const PROPERTY_DISCOUNT_PRICE = 'discountPrice';
    public const PROPERTY_DISCOUNT_FROM = 'discountFrom';
    public const PROPERTY_DISCOUNT_TO = 'discountTo';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_ATTRIBUTES = 'attributes';
    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_UNIQUE_IDENTIFIER = 'uniqueIdentifier';

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $locationId;

    /**
     * @var string
     */
    private string $price;

    /**
     * @var string|null
     */
    private ?string $discountPrice = null;

    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $discountFrom = null;

    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $discountTo = null;

    /**
     * @var int
     */
    private int $status = self::STATUS_DISABLED;

    /**
     * @var array
     */
    private array $attributes = [];

    /**
     * @var string
     */
    private string $description = '';

    /**
     * @var string
     */
    private string $uniqueIdentifier;

    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $deletedAt = null;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocationId(): string
    {
        return $this->locationId;
    }

    /**
     * @param string $locationId
     */
    public function setLocationId(string $locationId): void
    {
        $this->locationId = $locationId;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string|null
     */
    public function getDiscountPrice(): ?string
    {
        return $this->discountPrice;
    }

    /**
     * @param string $discountPrice
     */
    public function setDiscountPrice(string $discountPrice): void
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDiscountFrom(): ?DateTimeImmutable
    {
        return $this->discountFrom;
    }

    /**
     * @param DateTimeImmutable|null $discountFrom
     */
    public function setDiscountFrom(?DateTimeImmutable $discountFrom): void
    {
        $this->discountFrom = $discountFrom;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDiscountTo(): ?DateTimeImmutable
    {
        return $this->discountTo;
    }

    /**
     * @param DateTimeImmutable|null $discountTo
     */
    public function setDiscountTo(?DateTimeImmutable $discountTo): void
    {
        $this->discountTo = $discountTo;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    /**
     * @param string $uniqueIdentifier
     */
    public function setUniqueIdentifier(string $uniqueIdentifier): void
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'id' => $this->getId(),
            static::PROPERTY_NAME => $this->getName(),
            static::PROPERTY_LOCATION_ID => $this->getLocationId(),
            static::PROPERTY_PRICE => $this->getPrice(),
            static::PROPERTY_DISCOUNT_PRICE => $this->getDiscountPrice(),
            static::PROPERTY_DISCOUNT_FROM => $this->getDiscountFrom(),
            static::PROPERTY_DISCOUNT_TO => $this->getDiscountTo(),
            static::PROPERTY_STATUS => $this->getStatus(),
            static::PROPERTY_ATTRIBUTES => $this->getAttributes(),
            static::PROPERTY_DESCRIPTION => $this->getDescription(),
            static::PROPERTY_UNIQUE_IDENTIFIER => $this->getUniqueIdentifier(),
            'createdAt' => $this->getCreatedAt()->format(\DateTimeInterface::ISO8601),
            'updatedAt' => null,
            'deletedAt' => null,
        ];
        if ($this->getUpdatedAt() instanceof DateTimeImmutable) {
            $array['updatedAt'] = $this->getUpdatedAt()->format(\DateTimeInterface::ISO8601);
        }
        if ($this->getDeletedAt() instanceof DateTimeImmutable) {
            $array['deletedAt'] = $this->getDeletedAt()->format(\DateTimeImmutable::ISO8601);
        }
        return $array;
    }

    /**
     * @param array $product
     * @return Product
     */
    public static function fromArray(array $product): Product
    {
        v::arrayType()
            ->notEmpty()
            ->key(static::PROPERTY_NAME, v::stringType()->notEmpty(), true)
            ->key(static::PROPERTY_LOCATION_ID, v::stringType()->notEmpty()->uuid(4), true)
            ->key(static::PROPERTY_PRICE, v::stringType()->numericVal(), true)
            ->key(static::PROPERTY_DISCOUNT_PRICE, v::oneOf(
                v::nullType(),
                v::stringType()->numericVal()
            ), true)
            ->key(static::PROPERTY_DISCOUNT_FROM, v::oneOf(
                v::nullType(),
                v::stringType()->dateTime(\DateTimeInterface::ISO8601)
            ), true)
            ->key(static::PROPERTY_DISCOUNT_TO, v::oneOf(
                v::nullType(),
                v::stringType()->dateTime(\DateTimeInterface::ISO8601)
            ), true)
            ->key(static::PROPERTY_STATUS, v::oneOf(
                v::intType()->equals(static::STATUS_ENABLED),
                v::intType()->equals(static::STATUS_DISABLED)
            ), true)
            ->key(static::PROPERTY_ATTRIBUTES, v::arrayType(), true)
            ->key(static::PROPERTY_DESCRIPTION, v::stringType(), true)
            ->key(static::PROPERTY_UNIQUE_IDENTIFIER, v::stringType()->notEmpty(), true)
            ->check($product);
        $entity = new self;
        $entity->setName($product[static::PROPERTY_NAME]);
        $entity->setLocationId($product[static::PROPERTY_LOCATION_ID]);
        $entity->setPrice($product[static::PROPERTY_PRICE]);
        $entity->setDiscountPrice($product[static::PROPERTY_DISCOUNT_PRICE]);
        if ($product[static::PROPERTY_DISCOUNT_FROM] !== null) {
            $entity->setDiscountFrom(DateTimeImmutable::createFromFormat(
                \DateTimeInterface::ISO8601,
                $product[static::PROPERTY_DISCOUNT_FROM]
            ));
        }
        if ($product[static::PROPERTY_DISCOUNT_TO] !== null) {
            $entity->setDiscountTo(DateTimeImmutable::createFromFormat(
                \DateTimeInterface::ISO8601,
                $product[static::PROPERTY_DISCOUNT_TO]
            ));
        }
        $entity->setStatus($product[static::PROPERTY_STATUS]);
        $entity->setAttributes($product[static::PROPERTY_ATTRIBUTES]);
        $entity->setDescription($product[static::PROPERTY_DESCRIPTION]);
        $entity->setUniqueIdentifier($product[static::PROPERTY_UNIQUE_IDENTIFIER]);
        return $entity;
    }
}