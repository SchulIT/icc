<?php

namespace App\Repository;

use App\Entity\Week;

interface WeekRepositoryInterface {
    /**
     * @return Week[]
     */
    public function findAll(): array;
}