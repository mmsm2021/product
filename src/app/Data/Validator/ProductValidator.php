<?php

namespace App\Data\Validator;

use App\Database\Entities\Product;
use Respect\Validation\Validatable;
use Respect\Validation\Validator as v;

class ProductValidator
{

    protected const PROPERTY_NAME = 'name';
    protected const PROPERTY_LOCATION_ID = 'locationid';
    protected const PROPERTY_PRICE = 'price';
    protected const PROPERTY_DISCOUNT_PRICE = 'discountprice';
    protected const PROPERTY_DISCOUNT_FROM = 'discountfrom';
    protected const PROPERTY_DISCOUNT_TO = 'discountto';
    protected const PROPERTY_STATUS = 'status';
    protected const PROPERTY_ATTRIBUTES = 'attributes';
    protected const PROPERTY_DESCRIPTION = 'description';
    protected const PROPERTY_UNIQUE_IDENTIFIER = 'uniqueidentifier';

    /**
     * @var Validatable|null
     */
    private ?Validatable $postValidator = null;

    /**
     * @OA\Schema(
     *     schema="CreateProductDTO",
     *     type="object",
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         minLength=4,
     *         maxLength=200,
     *         nullable=false
     *     ),
     *     @OA\Property(
     *         property="locationId",
     *         ref="#/components/schemas/uuid",
     *         nullable=false
     *     ),
     *     @OA\Property(
     *         property="price",
     *         type="string",
     *         format="number",
     *         minLength=1,
     *         maxLength=50,
     *         nullable=false
     *     ),
     *     @OA\Property(
     *         property="discountPrice",
     *         type="string",
     *         format="number",
     *         minLength=1,
     *         maxLength=50,
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="discountFrom",
     *         ref="#/components/schemas/timestamp",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="discountTo",
     *         ref="#/components/schemas/timestamp",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="status",
     *         type="integer",
     *         nullable=false,
     *         description="Enabled=1, Disabled=0"
     *     ),
     *     @OA\Property(
     *         property="attributes",
     *         ref="#/components/schemas/FreeForm",
     *         nullable=false
     *     ),
     *     @OA\Property(
     *         property="description",
     *         type="string",
     *         nullable=true
     *     ),
     *     @OA\Property(
     *         property="uniqueIdentifier",
     *         type="string",
     *         nullable=false,
     *         minLength=4,
     *         maxLength=254
     *     )
     * )
     * @return Validatable
     */
    public function getPostValidator(): Validatable
    {
        if (!($this->postValidator instanceof Validatable)) {
            $this->postValidator = v::arrayType()
                ->notEmpty()
                ->key(static::PROPERTY_NAME, v::stringType()->notEmpty()->length(4,200), true)
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
                    v::intType()->equals(Product::STATUS_ENABLED),
                    v::intType()->equals(Product::STATUS_DISABLED)
                ), true)
                ->key(static::PROPERTY_ATTRIBUTES, v::arrayType(), true)
                ->key(static::PROPERTY_DESCRIPTION, v::stringType(), true)
                ->key(static::PROPERTY_UNIQUE_IDENTIFIER, v::stringType()->notEmpty(), true);
        }
        return $this->postValidator;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function postCheck(array $data): bool
    {
        $data = array_change_key_case($data, CASE_LOWER);
        $this->getPostValidator()->check($data);
        return true;
    }
}