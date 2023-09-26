<?php

namespace App\Command;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Symfony\Component\String\u;

#[AsCommand('app:audit:cleanup', 'Leert das Audit-Log für Entitäten, die importiert werden, um Speicherplatz zu sparen.')]
#[AsCronJob('@daily')]
class CleanupAuditLogCommand extends Command {
    private const ListOfTables = [
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

    public function __construct(private readonly int $retentionDays, private readonly EntityManagerInterface $em, private readonly DateHelper $dateHelper, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

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

        return 0;
    }
}