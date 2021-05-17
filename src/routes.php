<?php

use Slim\Routing\RouteCollectorProxy;

/** @var \Slim\App $app */

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->group('/api/v1', function(RouteCollectorProxy $group) {
    // add routes here.
});