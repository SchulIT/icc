<?php

namespace App\Command;

use App\Chat\Cleaner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:chats:purge', description: 'Löscht alle Chats.')]
class PurgeChatsCommand extends Command {
    public function __construct(private readonly Cleaner $cleaner, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $style->section('Lösche alle Chats');

        $style->caution('Diese Aktion kann nicht rückgängig gemacht werden');
        if($style->confirm('Sollen wirklich alle Chats gelöscht werden?', false) !== true) {
            $style->warning('Abgebrochen, keine Chats gelöscht.');
            return Command::SUCCESS;
        }

        $this->cleaner->cleanup();
        $style->success('Alle Chats gelöscht');

        return Command::SUCCESS;
    }
}