<?php

namespace App\Actions;

use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use App\Exceptions\EntityNotFoundException;
use MMSM\Lib\Factories\JsonResponseFactory;

use \Throwable;


class Delete {

    private ProductRepository $productRepository;
    private JsonResponseFactory $jsonResponseFactory;
    
    public function __construct(
        ProductRepository $productRepository,
        JsonResponseFactory $jsonResponseFactory
    ){
        
        $this->productRepository = $productRepository;
        $this->jsonResponseFactory = $jsonResponseFactory;
    }
    
    /**
    * @param Request $request
    * @param Response $response
    * @return Response
    */
    
    public function __invoke(Request $request, Response $response, string $productId)
    {
        try {
            $hard = false;
            $query = $request->getQueryParams();
            if(isset($query['hard']) && $query['hard'] == "true"){
              $hard = true;
            }
            $product = $this->productRepository->getById($productId, !$hard);
            $this->productRepository->delete($product, $hard);
            return $this->jsonResponseFactory->create(204);
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

?>