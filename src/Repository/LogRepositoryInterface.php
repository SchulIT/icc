<?php

namespace App\Repository;

use Gedmo\Loggable\Entity\LogEntry;

interface LogRepositoryInterface {

    /**
     * @param object $entity
     * @return LogEntry[]
     */
    public function getLogEntries($entity): array;

    /**
     * @param object $entity
     * @param int $version
     */
    public function revert($entity, int $version): void;
}