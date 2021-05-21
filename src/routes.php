<?php

use Slim\Routing\RouteCollectorProxy;
use App\Actions\Add;
use App\Actions\Read;
use App\Actions\Delete;
use App\Actions\ReadByLocation;

/** @var \Slim\App $app */
/** @var \Psr\Container\ContainerInterface $container */
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$authMiddleware = $container->get(\MMSM\Lib\AuthorizationMiddleware::class);
$bodyMiddleware = $container->get(\Slim\Middleware\BodyParsingMiddleware::class);

$app->group('/api/v1', function(RouteCollectorProxy $group) use ($authMiddleware, $bodyMiddleware) {
    $group->post('/products', Add::class)
        ->add($authMiddleware)
        ->add($bodyMiddleware);
    $group->get('/product/{productId}', Read::class);
    $group->delete('/product/{productId}', Delete::class)
        ->add($authMiddleware);
    $group->get('/products/{locationId}', ReadByLocation::class);
    
});