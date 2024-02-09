<?php

namespace App\Repository;

use App\Entity\ParentsDay;
use DateTime;

interface ParentsDayRepositoryInterface {

    /**
     * @param DateTime $from Starting date to search from
     * @return ParentsDay[]
     */
    public function findUpcoming(DateTime $from): array;

    /**
     * @return ParentsDay[]
     */
    public function findAll(): array;

    public function persist(ParentsDay $parentsDay): void;

    public function remove(ParentsDay $parentsDay): void;
}