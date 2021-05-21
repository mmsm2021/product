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


class Add {

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
    
    public function __invoke(Request $request, Response $response)
    {
        try {
            $product = Product::fromArray($request->getParsedBody());
            $product = $this->productRepository->save($product);
            return $this->jsonResponseFactory->create(200, $product->toArray());
            
            
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