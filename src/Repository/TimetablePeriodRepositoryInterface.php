<?php

namespace App\Repository;

use App\Entity\TimetablePeriod;

interface TimetablePeriodRepositoryInterface {

    /**
     * @param int $id
     * @return TimetablePeriod|null
     */
    public function findOneById(int $id): ?TimetablePeriod;

    /**
     * @param string $externalId
     * @return TimetablePeriod|null
     */
    public function findOneByExternalId(string $externalId): ?TimetablePeriod;

    /**
     * @return TimetablePeriod[]
     */
    public function findAll();

    /**
     * @param TimetablePeriod $period
     */
    public function persist(TimetablePeriod $period): void;

    /**
     * @param TimetablePeriod $period
     */
    public function remove(TimetablePeriod $period): void;
}