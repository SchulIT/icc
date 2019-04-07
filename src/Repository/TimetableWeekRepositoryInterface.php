<?php

namespace App\Repository;

use App\Entity\TimetableWeek;

interface TimetableWeekRepositoryInterface {

    /**
     * @param int $id
     * @return TimetableWeek|null
     */
    public function findOneById(int $id): ?TimetableWeek;

    /**
     * @param string $key
     * @return TimetableWeek|null
     */
    public function findOneByKey(string $key): ?TimetableWeek;

    /**
     * @return TimetableWeek[]
     */
    public function findAll();

    /**
     * @param TimetableWeek $week
     */
    public function persist(TimetableWeek $week): void;

    /**
     * @param TimetableWeek $week
     */
    public function remove(TimetableWeek $week): void;
}