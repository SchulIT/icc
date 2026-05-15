<?php

namespace App\Framework\Repository;

interface TransactionalRepositoryInterface {
    public function beginTransaction(): void;

    public function commit(): void;
}