<?php 

namespace App\Database\Entities;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use App\Database\EntityInterface;
use App\Database\Repositories\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class Product implements EntityInterface
{
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
     * @var string|null
     */
    private ?string $description = null;

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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
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
            'name' => $this->getName(),
            'locationId' => $this->getLocationId(),
            'price' => $this->getPrice(),
            'discountPrice' => $this->getDiscountPrice(),
            'discountFrom' => $this->getDiscountFrom(),
            'discountTo' => $this->getDiscountTo(),
            'status' => $this->getStatus(),
            'attributes' => $this->getAttributes(),
            'description' => $this->getDescription(),
            'uniqueIdentifier' => $this->getUniqueIdentifier(),
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
     * @param ClassMetadata $metadata
     */
    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable(ProductRepository::TABLE_NAME);
        $builder->setCustomRepositoryClass(ProductRepository::class);

        $builder->createField('id', Types::STRING)
            ->makePrimaryKey()
            ->nullable(false)
            ->length(40)
            ->build();

        $builder->createField('name', Types::STRING)
            ->nullable(false)
            ->length(200)
            ->build();

        $builder->createField('locationId', Types::STRING)
            ->nullable(false)
            ->length(40)
            ->columnName('location_id')
            ->build();

        $builder->createField('price', Types::STRING)
            ->nullable(true)
            ->build();

        $builder->createField('discountPrice', Types::STRING)
            ->columnName('discount_price')
            ->nullable(true)
            ->build();
        
        $builder->createField('discountFrom', Types::DATETIMETZ_IMMUTABLE)
            ->nullable(true)
            ->columnName('discount_from')
            ->build();

        $builder->createField('discountTo', Types::DATETIMETZ_IMMUTABLE)
            ->nullable(true)
            ->columnName('discount_to')
            ->build();

        $builder->createField('status', Types::BOOLEAN)
            ->nullable(false)
            ->build();

        $builder->createField('attributes', Types::JSON)
            ->nullable(true)
            ->build();

        $builder->createField('description', Types::TEXT)
            ->nullable(true)
            ->build();


        $builder->createField('createdAt', Types::DATETIMETZ_IMMUTABLE)
            ->nullable(false)
            ->columnName('created_at')
            ->build();

        $builder->createField('updatedAt', Types::DATETIMETZ_IMMUTABLE)
            ->nullable(true)
            ->columnName('updated_at')
            ->build();

        $builder->createField('deletedAt', Types::DATETIMETZ_IMMUTABLE)
            ->nullable(true)
            ->columnName('deleted_at')
            ->build();

        $builder->createField('uniqueIdentifier', Types::STRING)
            ->nullable(false)
            ->columnName('unique_identifier')
            ->build();
    }
}
