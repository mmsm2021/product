<?php

use App\Actions\Product\DeleteAction;
use App\Actions\Product\GetAction;
use App\Actions\Product\ListAction;
use App\Actions\Product\PatchAction;
use App\Actions\Product\PostAction;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteCollectorProxy;

/**
 * @OA\Info(title="ProductsAPI", version="1.0.0")
 */

/**
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="jwtauth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="uuid",
 *     type="string",
 *     format="uuid",
 *     description="Universally unique identifier 128-bits"
 * )
 */

/**
 * @OA\Schema(
 *     schema="jwt",
 *     type="string",
 *     format="jwt",
 *     description="A JSON Web Token",
 *     default="Bearer {id-token}"
 * )
 */

/**
 * @OA\Schema(
 *     schema="FreeForm",
 *     type="object",
 *     description="Key-value Pairs in a JSON Object.",
 *     @OA\AdditionalProperties(type="string"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="timestamp",
 *     type="string",
 *     format="timestamp",
 *     description="ISO-8806 timestamp format in PHP: Y-m-d\TH:i:sO"
 * )
 */

/**
 * @OA\Schema(
 *     schema="error",
 *     type="object",
 *     @OA\Property(
 *         property="error",
 *         type="boolean"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="array",
 *         @OA\Items(type="string")
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductList",
 *     type="array",
 *     @OA\Items(
 *         ref="#/components/schemas/Product"
 *     )
 * )
 */

/** @var \Slim\App $app */
/** @var \Psr\Container\ContainerInterface $container */
$app->addRoutingMiddleware();
$app->add($container->get(\Slim\Middleware\ErrorMiddleware::class));
$app->add($container->get(\Slim\Middleware\BodyParsingMiddleware::class));
$app->add($container->get(\MMSM\Lib\AuthorizationMiddleware::class));

$app->options('{routes:.+}', function (ResponseFactory $responseFactory) {
    return $responseFactory->createResponse(204);
});

$app->group('/api/v1', function(RouteCollectorProxy $group) {
    $group->get('/products', ListAction::class);
    $group->get('/products/{id}', GetAction::class);
    $group->post('/products', PostAction::class);
    $group->delete('/products/{id}', DeleteAction::class);
    $group->patch('/products/{id}', PatchAction::class);
});