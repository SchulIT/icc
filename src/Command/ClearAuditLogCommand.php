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

#[AsCommand('app:db:clear_audit', 'Leert das Audit-Log.')]
class ClearAuditLogCommand extends Command {

    public function __construct(private EntityManagerInterface $em, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

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
        return 0;
    }
}