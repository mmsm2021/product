<?php

use Slim\Routing\RouteCollectorProxy;
use App\Actions\Add;
use App\Actions\Read;



$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->group('/api/v1', function(RouteCollectorProxy $group) {
    $group->post('/products', Add::class);
    $group->get('/products/{productId}', Read::class);
});