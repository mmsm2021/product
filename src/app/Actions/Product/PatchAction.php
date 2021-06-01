<?php

namespace App\Actions\Product;

use App\Database\Repositories\ProductRepository;
use App\Exceptions\SaveException;
use MMSM\Lib\Authorizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpInternalServerErrorException;
use App\Exceptions\EntityNotFoundException;
use MMSM\Lib\Factories\JsonResponseFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;


class PatchAction
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
     * PatchAction constructor.
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
     * @OA\Patch(
     *     path="/api/v1/products/{id}",
     *     summary="Updates product from carried JSON",
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
     *     @OA\RequestBody(
     *         required=true,
     *         description="The Location that you want to create.",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductDTO"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Will reply with the created product in JSON format",
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
     * @throws HttpException
     */
    public function __invoke(Request $request, string $id): Response
    {
        try {
            $body = $request->getParsedBody();
            if (!is_array($body)) {
                throw new HttpBadRequestException(
                    $request,
                    'Invalid Body.',
                );
            }
            $this->authorizer->authorizeToRoles($request, [
                'user.roles.super',
                'user.roles.admin',
            ]);
            $isSuperAdmin = $this->authorizer->hasRole($request, 'user.roles.admin');
            $product = $this->productRepository->getById($id);

            if (!$isSuperAdmin && !$this->authorizer->isUserInLocation($request, $product->getLocationId())) {
                throw new HttpUnauthorizedException(
                    $request,
                    'You do not have access to modify that product.'
                );
            }

            foreach($body as $key => $value){
                $lowerKey = strtolower($key);
                switch ($lowerKey) {
                    case 'name':
                        $product->setName($value); 
                        break;
                    case 'locationid':
                        if (!$isSuperAdmin && !$this->authorizer->isUserInLocation($request, $value)) {
                            throw new HttpUnauthorizedException(
                                $request,
                                'You do not have access to move that product to that location.'
                            );
                        }
                        $product->setLocationId($value);
                        break;
                    case 'price':
                        $product->setPrice($value); 
                        break;
                    case 'discountprice':
                        $product->setDiscountPrice($value); 
                        break;
                    case 'discountfrom':
                        $product->setDiscountFrom($value);
                        break;
                    case 'discountto':
                        $product->setDiscountTo($value);
                        break;
                    case 'status':
                        $product->setStatus($value);
                        break;
                    case 'attributes':
                        $product->setAttributes($value);
                        break;
                    case 'description':
                        $product->setDescription($value);
                        break;
                }
            }
            
            $product = $this->productRepository->save($product);
            return $this->jsonResponseFactory->create(200, $product->toArray());
        } catch (EntityNotFoundException $entityNotFoundException) {
            throw new HttpNotFoundException(
                $request,
                'Failed to find product by id: "' . $id . '".',
                $entityNotFoundException
            );
        } catch (SaveException $exception) {
            throw new HttpInternalServerErrorException(
                $request,
                'Failed to save changes.',
                $exception
            );
        }
    }
}
