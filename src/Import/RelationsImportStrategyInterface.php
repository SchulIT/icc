<?php

namespace App\Import;

use App\Repository\TransactionalRepositoryInterface;

interface RelationsImportStrategyInterface {

    public function getRepository(): TransactionalRepositoryInterface;

    public function removeAll(): void;

    public function persist($data): void;
}