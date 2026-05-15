<?php

namespace App\Infrastructure\Command;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;
use function Symfony\Component\String\u;

#[AsCommand('app:audit:cleanup', 'Leert das Audit-Log für Entitäten, die importiert werden, um Speicherplatz zu sparen.')]
#[AsCronTask('@daily')]
readonly class CleanupAuditLogCommand {

    public function __construct(private int $retentionDays, private EntityManagerInterface $em, private DateHelper $dateHelper) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $auditTables = array_filter(
            $this->em->getConnection()->createSchemaManager()->introspectTables(),
            fn(Table $table) => u($table->getObjectName()->getUnqualifiedName()->getValue())->endsWith('_audit')
        );

        $auditTableNames = array_map(fn(Table $table): string => $table->getObjectName()->getUnqualifiedName()->getValue(), $auditTables);

        $style->section(sprintf('Leere %d Audit-Tabellen', count($auditTableNames)));

        if($this->retentionDays === 0) {
            $style->success('Als Wert für die Aufbewahrung wurde 0 eingetragen, behalte vollständiges Auditlog.');
            return 0;
        }

        foreach($auditTableNames as $table) {
            $style->writeln('> Leere ' . $table);

            $this->em->getConnection()->executeQuery(
                'DELETE FROM ' . $table . ' WHERE created_at < ?',
                [
                    $this->dateHelper->getToday()->modify(sprintf('-%d days', $this->retentionDays))->format('Y-m-d')
                ]
            );
            $this->em->getConnection()->executeQuery('OPTIMIZE TABLE ' . $table);
        }

        $style->success('Fertig');

        return Command::SUCCESS;
    }
}