<?php

namespace App\Command;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Symfony\Component\String\u;

class ClearAuditLogCommand extends Command {
    private $em;

    public function __construct(EntityManagerInterface $em, string $name = null) {
        parent::__construct($name);
        $this->em = $em;
    }

    public function configure() {
        $this->setName('app:db:clear_audit')
            ->setDescription('Clears the audit log.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        /** @var Table[] $tables */
        $tables = array_filter(
            $this->em->getConnection()->getSchemaManager()->listTables(),
            function(Table $table) {
                return u($table->getName())->endsWith('_audit');
            });

        $style->section(sprintf('Clear %d audit tables', count($tables)));

        foreach($tables as $table) {
            $style->writeln('> Clear ' . $table->getName());
            $this->em->getConnection()->executeQuery('DELETE FROM ' . $table->getName());
        }

        $style->success('All audit logs cleared.');
        return 0;
    }
}