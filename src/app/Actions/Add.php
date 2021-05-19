<?php

namespace App\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use \Throwable;


class Add {
    
    public function __construct(){
        

    }
    
    
    
    public function __invoke(Response $response, $orderId)
    {
        try {

            $response = $orderId;
            return $response;
        } catch (Throwable $e) {
            return $response;
        }
    }
}


?>