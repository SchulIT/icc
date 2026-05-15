<?php

namespace App\Timetable\Repository;

use App\Timetable\Entity\Week;

interface WeekRepositoryInterface {
    /**
     * @return Week[]
     */
    public function findAll(): array;
}