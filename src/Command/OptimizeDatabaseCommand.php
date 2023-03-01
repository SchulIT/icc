<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob('@monthly')]
#[AsCommand('app:db:optimize', 'Optimiert alle Datenbanktabellen mit dem OPTIMIZE-Befehl (MariaDB)')]
class OptimizeDatabaseCommand extends Command {
    public function __construct(private EntityManagerInterface $em, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $tables = $this->em->getConnection()->createSchemaManager()->listTables();

        $style->section(sprintf('Optimiere %d Tabellen', count($tables)));

        foreach($tables as $table) {
            $style->writeln('> Optimiere ' . $table->getName());
            $this->em->getConnection()->executeQuery('OPTIMIZE TABLE ' . $table->getName());
        }

        $style->success('Fertig');
        return 0;
    }
}