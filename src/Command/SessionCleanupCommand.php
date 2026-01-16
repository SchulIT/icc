<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand('app:security:remove_old_sessions', description: 'Löscht alte Sessions aus der Datenbank')]
#[AsCronTask('@daily')]
class SessionCleanupCommand {

    public const string SessionTableName = 'sessions';
    public const string SessionLifetypeColumnName = 'sess_lifetime';
    public const int ThresholdInSeconds = 600; // 10min

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DateHelper $dateHelper
    ) {

    }

    public function __invoke(SymfonyStyle $style): int {
        $sql = sprintf("SHOW TABLES LIKE '%s';", self::SessionTableName);
        $row = $this->em->getConnection()->executeQuery($sql);

        if($row->fetchAssociative() === false) {
            $style->error('Session-Tabelle existiert nicht. Wurde `php bin/console app:setup` ausgeführt?');
            return Command::FAILURE;
        }

        $threshold = $this->dateHelper->getNow()->modify(sprintf('-%d seconds', self::ThresholdInSeconds));
        $sql = sprintf(
            "DELETE FROM %s WHERE %s < %d",
            self::SessionTableName,
            self::SessionLifetypeColumnName,
            $threshold->getTimestamp()
        );

        $this->em->getConnection()->executeQuery($sql);
        $style->success('Sessions-Tabelle aufgeräumt');

        return Command::SUCCESS;
    }
}