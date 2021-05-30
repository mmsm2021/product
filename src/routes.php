<?php

use App\Actions\Product\DeleteAction;
use App\Actions\Product\GetAction;
use App\Actions\Product\PostAction;
use Slim\Routing\RouteCollectorProxy;

/** @var \Slim\App $app */
/** @var \Psr\Container\ContainerInterface $container */
$app->addRoutingMiddleware();
$app->add($container->get(\Slim\Middleware\ErrorMiddleware::class));
$app->add($container->get(\Slim\Middleware\BodyParsingMiddleware::class));
$app->add($container->get(\MMSM\Lib\AuthorizationMiddleware::class));

$app->group('/api/v1', function(RouteCollectorProxy $group) {
    $group->get('/products/{id}', GetAction::class);
    $group->post('/products', PostAction::class);
    $group->delete('/products/{id}', DeleteAction::class);
    //$group->post('/products', Add::class);
    //$group->patch('/products/{productId}', Update::class);
    //$group->get('/products/{productId}', Read::class);
    //$group->get('/products/{locationId}', ReadByLocation::class);
    //$group->delete('/products/{productId}', Delete::class)->add($authMiddleware);
});