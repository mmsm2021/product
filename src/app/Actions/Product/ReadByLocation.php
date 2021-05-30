<?php

namespace App\Actions\Product;

use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use MMSM\Lib\Factories\JsonResponseFactory;
use \Throwable;

class ReadByLocation {

    private ProductRepository $productRepository;

    private JsonResponseFactory $jsonResponseFactory;

    /**
     * Read constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository, JsonResponseFactory $jsonResponseFactory)
    {
        $this->productRepository = $productRepository;
        $this->jsonResponseFactory = $jsonResponseFactory;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $locationId
     * @return Response
     * @throws HttpInternalServerErrorException
     */
    public function __invoke(Request $request, Response $response, string $locationId): Response
    {
        try {
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
        }
    }
}
