<?php

namespace App\Repository;

interface TransactionalRepositoryInterface {
    public function beginTransaction(): void;

    public function commit(): void;
}