<?php

namespace App\Import;

use App\Repository\TransactionalRepositoryInterface;

interface ReplaceImportStrategyInterface {

    public function getRepository(): TransactionalRepositoryInterface;

    public function removeAll(): void;

    public function persist($data): void;
}