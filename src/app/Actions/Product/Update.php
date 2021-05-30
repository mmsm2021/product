<?php

namespace App\Actions\Product;

use App\Database\Entities\Product;
use App\Database\Repositories\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use App\Exceptions\EntityNotFoundException;
use MMSM\Lib\Factories\JsonResponseFactory;

use \Throwable;


class Update {

    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @var JsonResponseFactory
     */
    private JsonResponseFactory $jsonResponseFactory;
    
    public function __construct(
        ProductRepository $productRepository,
        JsonResponseFactory $jsonResponseFactory
    ) {
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
            $product = $this->productRepository->getById($productId);
            $changes = $request->getParsedBody();

            foreach($changes as $key => $value){
                switch($key){
                    case "name":
                        $product->setName($value); 
                        break;
                    case "price":
                        $product->setPrice($value); 
                        break;
                    case "discountPrice":
                        $product->setDiscountPrice($value); 
                        break;
                    case "discountFrom":
                        $product->setDiscountFrom($value);
                        break;
                    case "discountTo":
                        $product->setDiscountTo($value);
                        break;
                    case "status":
                        $product->setStatus($value);
                        break;
                    case "attributes":
                        $product->setAttributes($value);
                        break;
                    case "description":
                        $product->setDescription($value);
                        break;
                }
            }
            
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