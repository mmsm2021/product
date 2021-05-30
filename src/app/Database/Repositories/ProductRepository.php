<?php

namespace App\Database\Repositories;

use App\Database\Entities\Product;
use App\Exceptions\EntityNotFoundException;
use App\Exceptions\SaveException;
use App\Exceptions\DeleteException;
use Doctrine\ORM\EntityManager;

class ProductRepository{

    public const TABLE_NAME = 'products';

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;


    
    /**
     * @var ReflectionProperty
     */
    private \ReflectionProperty $updatedProperty;

     /**
     * @var ReflectionProperty
     */
    private \ReflectionProperty $deletedProperty;

    /**
     * ProductRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->updatedProperty = new \ReflectionProperty(Product::class, 'updatedAt');
        $this->deletedProperty = new \ReflectionProperty(Product::class, 'deletedAt');
    }

    /**
    * @param string $id
    * @return bool
    */
    public function idExists(string $id): bool
    {
        try {
            $this->getById($id);
            return true;
        } catch (EntityNotFoundException $exception) {
            return false;
        }
    }

    /**
     * @param string $id
     * @param bool $includeDeleted
     * @return Product
     * @throws EntityNotFoundException
     */
    public function getById(string $id, bool $includeDeleted = false): Product
    {
        $dql = 'SELECT p FROM ' . Product::class . ' p WHERE p.id = ?1';
        if(!$includeDeleted){
            $dql .= ' AND p.deletedAt IS NULL';
        }
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter(1, $id);
        $results = $query->getResult();
        if (empty($results)) {
            throw new EntityNotFoundException('Failed to find Product by id: "' . $id . '".');
        }
        return $results[array_keys($results)[0]];
    }

     /**
     * @param string $id
     * @return array $products
     * @throws EntityNotFoundException
     */
    public function getByLocationId(string $locationId): array
    {
        $query = $this->entityManager->createQuery('SELECT p FROM ' . Product::class . ' p WHERE p.locationId = ?1 AND p.deletedAt IS NULL');
        $query->setParameter(1, $locationId);
        $results = $query->getResult();
        if (empty($results)) {
            throw new EntityNotFoundException('Failed to find Product by Location id: "' . $locationId . '".');
        }
        return $results;
    }


    /**
     * @param Product $product
     * @return Product
     * @throws SaveException
     */
    public function save(Product $product) : Product
    {
        try{
            if ($this->idExists($product->getId())) {
                $this->markEntityAsUpdated($product);
            }
            $this->persist($product);
            return $product;

        }catch (\Throwable $exception) {
            throw new SaveException('Failed to save Product Entity.', $exception->getCode(), $exception);
        }
    }

    /**
     * @param Product $product
     * @param bool $hard
     * @throws DeleteException
     */
    public function delete(Product $product, bool $hard = false)
    {
        try {
            if ($hard) {
                $this->entityManager->remove($product);
                $this->entityManager->flush();
                return;
            }
            $this->markEntityAsDeleted($product);
            $this->persist($product);
        } catch (\Exception $exception) {
            throw new DeleteException('Failed to delete Product Entity.', $exception->getCode(), $exception);
        }
    }


    /**
    * @param Product $product
    */
    protected function markEntityAsUpdated(Product $product)
    {
        $this->updatedProperty->setAccessible(true);
        $this->updatedProperty->setValue($product, new \DateTimeImmutable('now'));
        $this->updatedProperty->setAccessible(false);
    }

    /**
    * @param Product $product
    */
    protected function markEntityAsDeleted(Product $product)
    {
        $this->deletedProperty->setAccessible(true);
        $this->deletedProperty->setValue($product, new \DateTimeImmutable('now'));
        $this->deletedProperty->setAccessible(false);
    }


     /**
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function persist(Product $product)
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
