<?php

namespace App\Database\Repositories;

use App\Database\Entities\Product;
use App\Exceptions\EntityNotFoundException;
use Doctrine\ORM\EntityManager;

class ProductRepository{

    public const TABLE_NAME = 'products';

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * ProductRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $id
     * @return Product
     * @throws EntityNotFoundException
     */
    public function getById(string $id): Product
    {
        $query = $this->entityManager->createQuery('SELECT p FROM ' . Product::class . ' p WHERE p.id = ?1');
        $query->setParameter(1, $id);
        $results = $query->getResult();
        if (empty($results)) {
            throw new EntityNotFoundException('Failed to find Product by id: "' . $id . '".');
        }
        return $results[array_keys($results)[0]];
    }
}
