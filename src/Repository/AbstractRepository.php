<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository {
    protected $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
}