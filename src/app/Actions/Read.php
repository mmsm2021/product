<?php

namespace App\Actions;

use App\Database\Repositories\ProductRepository;
use Psr\Http\Message\ResponseInterface as Response;
use \Throwable;

class Read {

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(Response $response, $productId)
    {
        try {
            $response->getBody()->write($this->productRepository->getById($productId));
            return $response;
        } catch (Throwable $e) {
            return $response;
        }
    }
}

?>