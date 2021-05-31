<?php

namespace App\Actions\Product;

use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use App\Exceptions\EntityNotFoundException;
use MMSM\Lib\Authorizer;
use MMSM\Lib\Factories\JsonResponseFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class GetAction
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
     * @var Authorizer
     */
    private Authorizer $authorizer;

    /**
     * Read constructor.
     * @param ProductRepository $productRepository
     * @param JsonResponseFactory $jsonResponseFactory
     * @param Authorizer $authorizer
     */
    public function __construct(
        ProductRepository $productRepository,
        JsonResponseFactory $jsonResponseFactory,
        Authorizer $authorizer
    ) {
        $this->productRepository = $productRepository;
        $this->jsonResponseFactory = $jsonResponseFactory;
        $this->authorizer = $authorizer;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Returns a JSON object of a product",
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
     *         description="Will reply with the product in JSON format",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
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
     * @throws HttpNotFoundException
     */
    public function __invoke(
        Request $request,
        string $id
    ): Response {
        try {
            $isSuperAdmin = $this->authorizer->hasRole($request, 'user.roles.super', false);
            $product = $this->productRepository->getById(
                $id,
                $isSuperAdmin
            );

            if ($product->getStatus() != Product::STATUS_ENABLED && !$isSuperAdmin &&
                (
                    !$this->authorizer->hasRoles($request, ['user.roles.employee', 'user.roles.admin'], true) ||
                    !$this->authorizer->isUserInLocation($request, $product->getLocationId())
                )
            ) {
                throw new HttpNotFoundException(
                    $request,
                    'No active product found.'
                );
            }

            return $this->jsonResponseFactory->create(
                200,
                $this->productRepository->getById(
                    $id,
                    $isSuperAdmin
                )->toArray()
            );
        } catch (EntityNotFoundException $entityNotFoundException) {
            throw new HttpNotFoundException(
                $request,
                $entityNotFoundException->getMessage(),
                $entityNotFoundException
            );
        }
    }
}
