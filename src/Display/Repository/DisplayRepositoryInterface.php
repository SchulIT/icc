<?php

namespace App\Display\Repository;

use App\Display\Entity\Display;

interface DisplayRepositoryInterface {

    /**
     * @return Display[]
     */
    public function findAll(): array;

    public function persist(Display $display): void;

    public function remove(Display $display);
}