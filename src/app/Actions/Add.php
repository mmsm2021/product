<?php

namespace App\Actions;


class Add {
    
    
    
    
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