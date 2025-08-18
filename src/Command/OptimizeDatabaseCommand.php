<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('@monthly')]
#[AsCommand('app:db:optimize', 'Optimiert alle Datenbanktabellen mit dem OPTIMIZE-Befehl (MariaDB)')]
readonly class OptimizeDatabaseCommand {
    public function __construct(private EntityManagerInterface $em) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $tables = $this->em->getConnection()->createSchemaManager()->listTables();

        $style->section(sprintf('Optimiere %d Tabellen', count($tables)));

        foreach($tables as $table) {
            $style->writeln('> Optimiere ' . $table->getName());
            $this->em->getConnection()->executeQuery('OPTIMIZE TABLE ' . $table->getName());
        }

        $style->success('Fertig');
        return Command::SUCCESS;
    }
}