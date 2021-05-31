<?php

namespace App\Actions\Product;

use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use Doctrine\Common\Collections\Criteria;
use MMSM\Lib\Authorizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use MMSM\Lib\Factories\JsonResponseFactory;
use \Throwable;

class ListAction
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
     * @param Request $request
     * @return Response
     * @throws HttpInternalServerErrorException
     */
    public function __invoke(Request $request): Response
    {
        /*try {
            $products = [];
            foreach($this->productRepository->getByLocationId($locationId) as $product) {
                $products[] = $product->toArray();
            }
            return $this->jsonResponseFactory->create(200, $products);
        } catch (Throwable $e) {
            try {
                $response->getBody()->write(json_encode([
                    'error' => true,
                    'message' => $e->getMessage(),
                ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
                return $response;
            } catch (Throwable $exception) {
                throw new HttpInternalServerErrorException($request, 'An Error occurred.', $exception);
            }
        }*/
    }

    /**
     * @param Request $request
     * @return Criteria
     */
    protected function getCriteriaFromQuery(Request $request): Criteria
    {
        $query = $request->getQueryParams();
        $criteria = Criteria::create();

    }
}
