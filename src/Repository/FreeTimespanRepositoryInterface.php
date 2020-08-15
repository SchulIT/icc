<?php

namespace App\Repository;

use App\Entity\FreeTimespan;
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

    public function removeAll(): void;
}