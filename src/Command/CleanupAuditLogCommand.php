<?php

namespace App\Command;

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
    private const array ListOfTables = [
        'grade_membership',
        'free_timespan',
        'infotext',
        'student',
        'study_group',
        'study_group_membership',
        'substitution',
        'subject',
        'teacher',
        'timetable_lesson',
        'timetable_supervision',
        'tuition'
    ];

    public function __construct(private int $retentionDays, private EntityManagerInterface $em, private DateHelper $dateHelper) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $auditTables = array_filter(
            $this->em->getConnection()->createSchemaManager()->listTables(),
            fn(Table $table) => u($table->getName())->endsWith('_audit')
        );

        $auditTableNames = array_map(fn(Table $table) => $table->getName(), $auditTables);

        $style->section(sprintf('Leere %d Audit-Tabellen', count(self::ListOfTables)));

        if($this->retentionDays === 0) {
            $style->success('Als Wert für die Aufbewahrung wurde 0 eingetragen, behalte vollständiges Auditlog.');
            return 0;
        }

        foreach(self::ListOfTables as $table) {
            $auditTable = u($table)->append('_audit')->toString();
            $style->writeln('> Leere ' . $auditTable);

            if(!in_array($auditTable, $auditTableNames)) {
                $style->warning(sprintf('Tabelle %s existiert nicht, überspringe', $auditTable));
                continue;
            }

            $this->em->getConnection()->executeQuery(
                'DELETE FROM ' . $auditTable . ' WHERE created_at < ?',
                [
                    $this->dateHelper->getToday()->modify(sprintf('-%d days', $this->retentionDays))->format('Y-m-d')
                ]
            );
            $this->em->getConnection()->executeQuery('OPTIMIZE TABLE ' . $auditTable);
        }

        $style->success('Fertig');

        return Command::SUCCESS;
    }
}