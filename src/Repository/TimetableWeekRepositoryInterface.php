<?php

namespace App\Repository;

use App\Entity\TimetableWeek;

interface TimetableWeekRepositoryInterface {

    public function findOneById(int $id): ?TimetableWeek;

    public function findOneByKey(string $key): ?TimetableWeek;

    public function findOneByWeekNumber(int $number): ?TimetableWeek;

    /**
     * @return TimetableWeek[]
     */
    public function findAll();

    public function persist(TimetableWeek $week): void;

    public function remove(TimetableWeek $week): void;
}