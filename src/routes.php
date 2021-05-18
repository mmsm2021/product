<?php

use Slim\Routing\RouteCollectorProxy;
use App\Actions\Add;
use App\Actions\Read;


/** @var \Slim\App $app */
/** @var \Psr\Container\ContainerInterface $container */
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$authMiddleware = $container->get(\MMSM\Lib\AuthorizationMiddleware::class);

$app->group('/api/v1', function(RouteCollectorProxy $group) {
    $group->post('/products', Add::class);
    $group->get('/products/{productId}', Read::class);
})->add($authMiddleware);