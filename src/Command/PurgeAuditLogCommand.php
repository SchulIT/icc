<?php

namespace App\Command;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Symfony\Component\String\u;

#[AsCommand('app:audit:purge', 'Leert das gesamte Audit-Log.')]
readonly class PurgeAuditLogCommand {

    public function __construct(private EntityManagerInterface $em) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        /** @var Table[] $tables */
        $tables = array_filter(
            $this->em->getConnection()->createSchemaManager()->listTables(),
            fn(Table $table) => u($table->getName())->endsWith('_audit'));

        $style->section(sprintf('Leere %d Audit-Tabellen', count($tables)));

        foreach($tables as $table) {
            $style->writeln('> Leere ' . $table->getName());
            $this->em->getConnection()->executeQuery('DELETE FROM ' . $table->getName());
        }

        $style->success('Fertig');
        return Command::SUCCESS;
    }
}