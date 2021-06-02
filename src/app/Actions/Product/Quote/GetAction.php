<?php

namespace App\Actions\Product\Quote;

use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use App\Exceptions\EntityNotFoundException;
use MMSM\Lib\Authorizer;
use MMSM\Lib\Factories\JsonResponseFactory;
use MMSM\Lib\JwtHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class GetAction
{
    /**
     * @var JsonResponseFactory
     */
    private JsonResponseFactory $jsonResponseFactory;

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var Authorizer
     */
    private Authorizer $authorizer;

    /**
     * @var JwtHandler
     */
    private JwtHandler $jwtHandler;

    /**
     * GetAction constructor.
     * @param JsonResponseFactory $jsonResponseFactory
     * @param ProductRepository $productRepository
     * @param Authorizer $authorizer
     * @param JwtHandler $jwtHandler
     */
    public function __construct(
        JsonResponseFactory $jsonResponseFactory,
        ProductRepository $productRepository,
        Authorizer $authorizer,
        JwtHandler $jwtHandler
    ) {
        $this->jsonResponseFactory = $jsonResponseFactory;
        $this->productRepository = $productRepository;
        $this->authorizer = $authorizer;
        $this->jwtHandler = $jwtHandler;
    }

    /**
     * @OA\Schema(
     *     schema="Quote",
     *     type="object",
     *     @OA\Property(
     *         property="token",
     *         type="string",
     *         format="jwt"
     *     )
     * )
     * @OA\Get(
     *     path="/api/v1/products/quote/{id}",
     *     summary="Returns a quote for a product",
     *     tags={"Product"},
     *     security={{ "jwtauth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id of the product.",
     *         required=true,
     *         @OA\Schema(
     *             ref="#/components/schemas/uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Will reply with a quote for the product.",
     *         @OA\JsonContent(ref="#/components/schemas/Quote")
     *     ),
     *     @OA\Response(
     *         response=404,
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
     * @param string $id
     * @return Response
     */
    public function __invoke(
        Request $request,
        string $id
    ): Response {
        try {
            $isSuperAdmin = $this->authorizer->hasRole($request, 'user.roles.super', false);
            $product = $this->productRepository->getById($id, $isSuperAdmin);
            if ($product->getStatus() != Product::STATUS_ENABLED) {
                throw new HttpNotFoundException(
                    $request,
                    'Product not found.'
                );
            }
            $productArray = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'locationId' => $product->getLocationId(),
                'price' => $product->getPrice(),
                'attributes' => $product->getAttributes(),
                'description' => $product->getDescription(),
                'uniqueIdentifier' => $product->getUniqueIdentifier(),
            ];
            $now = new \DateTimeImmutable();
            if ($product->getDiscountPrice() !== null && (
                    $product->getDiscountFrom() === null || $now > $product->getDiscountFrom()
                ) && (
                    $product->getDiscountTo() === null || $now < $product->getDiscountTo()
                )
            ) {
                $productArray['price'] = $product->getDiscountPrice();
            }
            return $this->jsonResponseFactory->create(200, [
                'token' => $this->jwtHandler->create([
                    'product' => $productArray
                ], strtotime('+15 minutes'))
            ]);
        } catch (EntityNotFoundException $exception) {
            throw new HttpNotFoundException(
                $request,
                'Product not found.',
                $exception
            );
        }
    }
}