<?php

namespace App\Repository;

use Gedmo\Loggable\Entity\LogEntry;

interface LogRepositoryInterface {

    /**
     * @param $entity
     * @return LogEntry[]
     */
    public function getLogEntries($entity): array;

    /**
     * @param $entity
     * @param int $version
     */
    public function revert($entity, int $version): void;
}