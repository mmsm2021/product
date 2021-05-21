<?php

namespace App\Actions;

use App\Database\Repositories\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use \Throwable;

class ReadByLocation {

    private ProductRepository $productRepository;

    /**
     * Read constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $locationId
     * @return Response
     * @throws HttpInternalServerErrorException
     */
    public function __invoke(Request $request, Response $response, string $locationId): Response
    {
        try {
            $response->getBody()->write(
                json_encode(
                    $this->productRepository->getByLocationId($locationId),
                    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
                )
            );
            //$response->withBody();
            return $response;
        } catch (Throwable $e) {
            try {
                $response->getBody()->write(json_encode([
                    'error' => true,
                    'message' => $e->getMessage(),
                ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
                return $response;
            } catch (Throwable $exception) {
                throw new HttpInternalServerErrorException($request, 'An Error occurred.', $exception);
            }
        }
    }
}
