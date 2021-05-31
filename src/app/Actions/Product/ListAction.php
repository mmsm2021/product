<?php

namespace App\Actions\Product;

use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use MMSM\Lib\Authorizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use MMSM\Lib\Factories\JsonResponseFactory;

class ListAction
{

    private const FIELDS = [
        'id',
        'name',
        'locationId',
        'price',
        'discountPrice',
        'discountFrom',
        'discountTo',
        'status',
        'description',
        'uniqueIdentifier',
    ];

    private const TIMESTAMPS = [
        'createdAt',
        'updatedAt',
        'deletedAt',
    ];

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
     *     path="/api/v1/products",
     *     summary="Returns a JSON array of product objects",
     *     tags={"Product"},
     *     security={{ "jwtauth":{} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="locationId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="locationId[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="discountPrice",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountPrice[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="discountFrom",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountFrom[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="discountTo",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discountTo[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="uniqueIdentifier",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="uniqueIdentifier[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="createdAt",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="createdAt[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="updatedAt",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="updatedAt[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *          *     @OA\Parameter(
     *         name="deletedAt",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[eq]",
     *         in="query",
     *         required=false,
     *         description="Equals operation.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[gt]",
     *         in="query",
     *         description="Geater Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[lt]",
     *         in="query",
     *         description="Less Than operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[gte]",
     *         in="query",
     *         description="Geater Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[lte]",
     *         in="query",
     *         description="Less Than or Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[neq]",
     *         in="query",
     *         description="Not Equals operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[in]",
     *         in="query",
     *         description="IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[notin]",
     *         in="query",
     *         description="NOT IN operation. Comma seperated list.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[contains]",
     *         in="query",
     *         description="String Contains operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[startswith]",
     *         in="query",
     *         description="String Starts With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="deletedAt[endswith]",
     *         in="query",
     *         description="String Ends With operation.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Will reply with a list of products in JSON objects.",
     *         @OA\JsonContent(ref="#/components/schemas/ProductList")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Empty reply will happen with a invalid filter config is applied.",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="will contain a JSON object with a message.",
     *         @OA\JsonContent(ref="#/components/schemas/error")
     *     )
     * )
     * @param Request $request
     * @return Response
     * @throws HttpInternalServerErrorException
     */
    public function __invoke(Request $request): Response
    {
        $criteria = $this->getCriteriaFromQuery($request);
        if (!($criteria instanceof Criteria)) {
            return $this->jsonResponseFactory->create(204);
        }
        return $this->jsonResponseFactory->create(200,
            $this->productRepository->getList(
                $criteria,
                true
            )->toArray()
        );
    }

    /**
     * @param Request $request
     * @return Criteria|bool
     */
    protected function getCriteriaFromQuery(Request $request)
    {
        $query = $request->getQueryParams();
        $criteria = Criteria::create();
        $and = false;
        $isSuperAdmin = $this->authorizer->hasRole($request, 'user.roles.super', false);
        $isLocationSpecific = $this->authorizer->hasRoles($request, [
            'user.roles.admin',
            'user.roles.employee'
        ], false);
        $this->processFilters(self::FIELDS, $request, $criteria, $and);
        if ($isSuperAdmin) {
            $this->processFilters(self::TIMESTAMPS, $request, $criteria, $and);
        } else {
            if ($isLocationSpecific &&
                array_key_exists('status', array_change_key_case($query, CASE_LOWER))
            ) {
                $appMetadata = $this->authorizer->getAppMetadata($request, true);
                if (empty($appMetadata) ||
                    !isset($appMetadata['locations']) ||
                    !is_array($appMetadata['locations']) ||
                    empty($appMetadata['locations'])
                ) {
                    return false;
                }
                $this->handleAnd(
                    $criteria,
                    Criteria::expr()->in('locationId', $appMetadata['locations']),
                    $and
                );
            } else {
                $this->handleAnd(
                    $criteria,
                    Criteria::expr()->eq('status', Product::STATUS_ENABLED),
                    $and
                );
            }
        }
        if (!isset($query['size']) ||
            !is_numeric($query['size']) ||
            $query['size'] > 200 ||
            $query['size'] < 1
        ) {
            $criteria->setMaxResults(20);
        } else {
            $criteria->setMaxResults($query['size']);
        }
        if (!isset($query['offset']) ||
            !is_numeric($query['offset']) ||
            $query['offset'] < 0
        ) {
            $criteria->setFirstResult(0);
        } else {
            $criteria->setFirstResult($query['offset']);
        }
        return $criteria;
    }

    /**
     * @param array $filters
     * @param Request $request
     * @param Criteria $criteria
     * @param bool $and
     */
    protected function processFilters(
        array $filters,
        Request $request,
        Criteria $criteria,
        bool &$and
    ): void {
        foreach($request->getQueryParams() as $key => $value) {
            if (empty($value) ||
                !in_array($key, $filters)
            ) {
                continue;
            }
            if (is_string($value)) {
                if (strtolower($value) == 'null') {
                    $this->handleAnd($criteria, Criteria::expr()->isNull($key), $and);
                } else {
                    $this->handleAnd($criteria, Criteria::expr()->eq($key, $value), $and);
                }
            } elseif (is_array($value)) {
                $this->parseFilter($criteria, $key, $value, $and);
            }
        }
    }

    /**
     * @param Criteria $criteria
     * @param string $field
     * @param array $filters
     * @param bool $and
     */
    protected function parseFilter(
        Criteria $criteria,
        string $field,
        array $filters,
        bool &$and
    ): void {
        foreach ($filters as $filter => $value) {
            switch (strtolower($filter)) {
                case 'eq':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->eq($field, $value),
                        $and
                    );
                    break;
                case 'gt':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->gt($field, $value),
                        $and
                    );
                    break;
                case 'lt':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->lt($field, $value),
                        $and
                    );
                    break;
                case 'gte':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->gte($field, $value),
                        $and
                    );
                    break;
                case 'lte':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->lte($field, $value),
                        $and
                    );
                    break;
                case 'neq':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->neq($field, $value),
                        $and
                    );
                    break;
                case 'in':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->in($field, array_map('trim', explode(',', $value))),
                        $and
                    );
                    break;
                case 'notin':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->notIn($field, array_map('trim', explode(',', $value))),
                        $and
                    );
                    break;
                case 'contains':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->contains($field, $value),
                        $and
                    );
                    break;
                case 'startswith':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->startsWith($field, $value),
                        $and
                    );
                    break;
                case 'endswith':
                    $this->handleAnd(
                        $criteria,
                        Criteria::expr()->endsWith($field, $value),
                        $and
                    );
                    break;
            }
        }
    }

    /**
     * @param Criteria $criteria
     * @param Comparison $comparison
     * @param bool $and
     */
    protected function handleAnd(
        Criteria $criteria,
        Comparison $comparison,
        bool &$and
    ): void {
        if ($and) {
            $criteria->andWhere($comparison);
        } else {
            $criteria->where($comparison);
            $and = true;
        }
    }
}
