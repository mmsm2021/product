<?php

use Slim\Routing\RouteCollectorProxy;
use App\Actions\Add;
use App\Actions\Read;
use App\Actions\Delete;
use App\Actions\ReadByLocation;
use App\Actions\Update;

/** @var \Slim\App $app */
/** @var \Psr\Container\ContainerInterface $container */
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->add($container->get(\Slim\Middleware\BodyParsingMiddleware::class));
$authMiddleware = $container->get(\MMSM\Lib\AuthorizationMiddleware::class);

$app->group('/api/v1', function(RouteCollectorProxy $group) use ($authMiddleware) {
    $group->post('/products', Add::class);
    $group->patch('/products/{productId}', Update::class);
    $group->get('/products/{productId}', Read::class);
    //$group->get('/products/{locationId}', ReadByLocation::class);
    $group->delete('/products/{productId}', Delete::class)
        ->add($authMiddleware);
});