<?php

namespace App\Repository;

use App\Entity\Display;

interface DisplayRepositoryInterface {

    /**
     * @return Display[]
     */
    public function findAll(): array;

    public function persist(Display $display): void;

    public function remove(Display $display);
}