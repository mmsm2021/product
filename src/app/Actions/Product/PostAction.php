<?php

namespace App\Actions\Product;

use App\Data\Validator\ProductValidator;
use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use App\Exceptions\SaveException;
use DateTimeImmutable;
use MMSM\Lib\Authorizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use MMSM\Lib\Factories\JsonResponseFactory;
use Slim\Exception\HttpUnauthorizedException;

class PostAction
{
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var JsonResponseFactory
     */
    private JsonResponseFactory $jsonResponseFactory;

    /**
     * @var ProductValidator
     */
    private ProductValidator $productValidator;

    /**
     * @var Authorizer
     */
    private Authorizer $authorizer;

    /**
     * PostAction constructor.
     * @param ProductRepository $productRepository
     * @param JsonResponseFactory $jsonResponseFactory
     * @param ProductValidator $productValidator
     * @param Authorizer $authorizer
     */
    public function __construct(
        ProductRepository $productRepository,
        JsonResponseFactory $jsonResponseFactory,
        ProductValidator $productValidator,
        Authorizer $authorizer
    ) {
        $this->productRepository = $productRepository;
        $this->jsonResponseFactory = $jsonResponseFactory;
        $this->productValidator = $productValidator;
        $this->authorizer = $authorizer;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Creates new product from carried JSON",
     *     tags={"Product"},
     *     security={{ "jwtauth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="The Product that you want to create.",
     *         @OA\JsonContent(ref="#/components/schemas/CreateProductDTO"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Will reply with the created products in JSON format",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="will contain a JSON object with a message.",
     *         @OA\JsonContent(ref="#/components/schemas/error")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="will contain a JSON object with a message.",
     *         @OA\JsonContent(ref="#/components/schemas/error")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="will contain a JSON object with a message.",
     *         @OA\JsonContent(ref="#/components/schemas/error")
     *     )
     * )
     * @param Request $request
     * @return Response
     * @throws HttpInternalServerErrorException|HttpBadRequestException|HttpUnauthorizedException
     */
    public function __invoke(
        Request $request
    ): Response {
        try {
            $this->authorizer->authorizeToRoles(
                $request,
                [
                    'user.roles.super',
                    'user.roles.admin',
                ]
            );
            $isSuperAdmin = $this->authorizer->hasRole($request, 'user.roles.super');
            $body = $request->getParsedBody();
            if (!is_array($body)) {
                throw new HttpBadRequestException(
                    $request,
                    'Invalid Body.'
                );
            }
            $this->productValidator->postCheck($body);
            $body = array_change_key_case($body, CASE_LOWER);
            $product = new Product;
            $product->setName($body['name']);
            if (!$isSuperAdmin && !$this->authorizer->isUserInLocation($request, $body['locationid'])) {
                throw new HttpUnauthorizedException(
                    $request,
                    'You cannot create products for locations which you are not a member of.'
                );
            }
            $product->setLocationId($body['locationid']);
            $product->setPrice($body['price']);
            if(isset($body['discountprice'])){
                $product->setDiscountPrice($body['discountprice']);
            }
            if (isset($body['discountfrom'])) {
                $product->setDiscountFrom(DateTimeImmutable::createFromFormat(
                    \DateTimeInterface::ISO8601,
                    $body['discountfrom']
                ));
            }
            if (isset($body['discountto'])) {
                $product->setDiscountTo(DateTimeImmutable::createFromFormat(
                    \DateTimeInterface::ISO8601,
                    $body['discountto']
                ));
            }
            $product->setStatus($body['status']);
            $product->setAttributes($body['attributes']);
            if (isset($body['description'])) {
                $product->setDescription($body['description']);
            }
            $product->setUniqueIdentifier($body['uniqueidentifier']);
            $product = $this->productRepository->save($product);
            return $this->jsonResponseFactory->create(200, $product->toArray());
        } catch (SaveException $exception) {
            throw new HttpInternalServerErrorException(
                $request,
                'An Error occurred.',
                $exception
            );
        }
    }
}
