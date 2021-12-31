<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("@monthly")
 */
class OptimizeDatabaseCommand extends Command {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, string $name = null) {
        parent::__construct($name);
        $this->em = $em;
    }

    public function configure() {
        $this->setName('app:db:optimize')
            ->setDescription('Optimizes all database tables using an OPTIMIZE query.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $tables = $this->em->getConnection()->createSchemaManager()->listTables();

        $style->section(sprintf('Optimize %d tables', count($tables)));

        foreach($tables as $table) {
            $style->writeln('> Optimize ' . $table->getName());
            $this->em->getConnection()->executeQuery('OPTIMIZE TABLE ' . $table->getName());
        }

        $style->success('All tables optimized.');
        return 0;
    }
}