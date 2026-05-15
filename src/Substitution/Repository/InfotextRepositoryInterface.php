<?php

namespace App\Substitution\Repository;

use App\Substitution\Entity\Infotext;
use App\Framework\Repository\TransactionalRepositoryInterface;
use DateTime;

interface InfotextRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @return Infotext[]
     */
    public function findAll(): array;

    /**
     * @param \DateTime $dateTime
     * @return Infotext[]
     */
    public function findAllByDate(DateTime $dateTime): array;

    /**
     * @param Infotext $infotext
     */
    public function persist(Infotext $infotext): void;

    public function removeAll(?DateTime $dateTime = null): void;

    public function removeBetween(DateTime $start, DateTime $end): int;
}