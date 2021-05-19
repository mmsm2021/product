<?php

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use \Throwable;

class Read {

    public function __invoke(Response $response, $productId)
    {
        try {
            $response->getBody()->write($productId);
            return $response;
        } catch (Throwable $e) {
            return $response;
        }
    }
}

?>