<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use DateTime;

interface TimetableSupervisionRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return TimetableSupervision|null
     */
    public function findOneById(int $id): ?TimetableSupervision;

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param Teacher $teacher
     * @return TimetableSupervision[]
     */
    public function findAllByTeacher(DateTime $startDate, DateTime $endDate, Teacher $teacher): array;

    /**
     * @param TimetableSupervision $supervision
     */
    public function persist(TimetableSupervision $supervision): void;

    /**
     * @param TimetableSupervision $supervision
     */
    public function remove(TimetableSupervision $supervision): void;

    /**
     * @param DateTime $start The date from which supervisions are removed (which is inclusive)
     * @param DateTime $end The date until supervisions are removed (which is inclusive)
     * @return int Number of removed supervisions
     */
    public function removeBetween(DateTime $start, DateTime $end): int;
}