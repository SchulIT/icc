<?php

namespace App\Repository;

use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

class LogRepository extends AbstractRepository implements LogRepositoryInterface {

    private function getRepository(): LogEntryRepository {
        /** @var LogEntryRepository $repo */
        $repo = $this->em->getRepository(LogEntry::class);
        return $repo;
    }

    /**
     * @inheritDoc
     */
    public function getLogEntries($entity): array {
        /** @var LogEntry[] $entries */
        $entries = $this->getRepository()->getLogEntries($entity);
        return $entries;
    }

    /**
     * @inheritDoc
     */
    public function revert($entity, int $version): void {
        $this->getRepository()->revert($entity, $version);
    }
}