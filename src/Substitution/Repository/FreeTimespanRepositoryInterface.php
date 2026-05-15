<?php

namespace App\Substitution\Repository;

use App\Framework\Repository\TransactionalRepositoryInterface;
use App\Substitution\Entity\FreeTimespan;
use DateTime;

interface FreeTimespanRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param DateTime $dateTime
     * @return FreeTimespan[]
     */
    public function findAllByDate(DateTime $dateTime): array;

    /**
     * @return FreeTimespan[]
     */
    public function findAll(): array;

    public function persist(FreeTimespan $timespan): void;

    public function removeAll(?DateTime $dateTime): void;

    public function removeBetween(DateTime $start, DateTime $end): int;
}