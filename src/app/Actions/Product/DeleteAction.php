<?php

namespace App\Actions\Product;

use App\Database\Repositories\ProductRepository;
use App\Exceptions\DeleteException;
use MMSM\Lib\Authorizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use App\Exceptions\EntityNotFoundException;
use MMSM\Lib\Factories\JsonResponseFactory;
use Slim\Exception\HttpUnauthorizedException;


class DeleteAction
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
     * Delete constructor.
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
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete af given product by id.",
     *     tags={"Product"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id of the product.",
     *         required=true,
     *         @OA\Schema(
     *             ref="#/components/schemas/uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="hard",
     *         in="query",
     *         description="Determines if it is a hard or soft delete.",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean",
     *             default=false
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Will reply with an empty body if successful.",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="will contain a JSON object with a message.",
     *         @OA\JsonContent(ref="#/components/schemas/error")
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="will contain a JSON object with a message happends if the entity could not be found by the given id.",
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
     * @throws HttpInternalServerErrorException
     */
    public function __invoke(
        Request $request,
        string $id
    ): Response {
        try {
            $query = $request->getQueryParams();
            $this->authorizer->authorizeToRoles($request, [
                'user.roles.super',
                'user.roles.admin',
            ]);
            $isSuperAdmin = $this->authorizer->hasRole($request, 'user.roles.super');
            $hard = (isset($query['hard']) &&
                $query['hard'] == "true" &&
                $isSuperAdmin);
            $product = $this->productRepository->getById($id, $isSuperAdmin);
            if (!$isSuperAdmin &&
                !$this->authorizer->isUserInLocation($request, $product->getLocationId())
            ) {
                throw new HttpUnauthorizedException(
                    $request,
                    'You are not authorized to do that.'
                );
            }
            $this->productRepository->delete($product, $hard);
            return $this->jsonResponseFactory->create(204);
        } catch (DeleteException $e) {
            throw new HttpInternalServerErrorException($request, $e->getMessage(), $e);
        } catch (EntityNotFoundException $entityNotFoundException) {
            return $this->jsonResponseFactory->create(410, [
                'error' => true,
                'message' => ['Entity is gone.']
            ]);
        }
    }
}

?>