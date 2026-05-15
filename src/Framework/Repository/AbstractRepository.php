<?php

namespace App\Framework\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository {
    public function __construct(protected EntityManagerInterface $em)
    {
    }
}