<?php

namespace App\Repository;

abstract class AbstractTransactionalRepository extends AbstractRepository implements TransactionalRepositoryInterface {

    private $isTransactionActive = false;

    protected function flushIfNotInTransaction() {
        $this->isTransactionActive || $this->em->flush();
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isTransactionActive = true;
    }

    public function commit(): void {
        $this->em->flush();
        $this->em->commit();
        $this->isTransactionActive = false;
    }
}