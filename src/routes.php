<?php

use Slim\Routing\RouteCollectorProxy;
use App\Actions\Add;
use App\Actions\Read;
use MMSM\Lib\AuthorizationMiddleware;

/** @var \Slim\App $app */
/** @var \Psr\Container\ContainerInterface $container */
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$authMiddleware = $container->get(\MMSM\Lib\AuthorizationMiddleware::class);

$app->group('/api/v1', function(RouteCollectorProxy $group) use ($authMiddleware) {
    $group->post('/products', Add::class)->add($authMiddleware);
    $group->get('/products/{productId}', Read::class);
}); #->add($authMiddleware)