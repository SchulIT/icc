<?php

namespace App\Repository;

use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;

interface TimetableSupervisionRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return TimetableSupervision|null
     */
    public function findOneById(int $id): ?TimetableSupervision;

    /**
     * @param TimetablePeriod $period
     * @return TimetableSupervision[]
     */
    public function findAllByPeriod(TimetablePeriod $period);

    /**
     * @return TimetableSupervision[]
     */
    public function findAll();

    /**
     * @param TimetableSupervision $supervision
     */
    public function persist(TimetableSupervision $supervision): void;

    /**
     * @param TimetableSupervision $supervision
     */
    public function remove(TimetableSupervision $supervision): void;
}