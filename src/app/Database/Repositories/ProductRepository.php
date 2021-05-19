<?php

namespace App\Database\Repositories;

use Doctrine\ORM\EntityManager;

class ProductRepository{


    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function getById($id){
        $query = $this->entityManager->createQuery('SELECT product FROM Pro.duct product WHERE ?1');
        $query->setParameter(1, $id);
        return $query->getResult();
    }
}



?>