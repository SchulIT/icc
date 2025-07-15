<?php

namespace App\Command;

use App\Chat\Cleaner;
use App\Repository\ChatRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:chats:archive', description: 'Archiviert alle Chats.')]
readonly class ArchiveChatsCommand {
    public function __construct(private ChatRepositoryInterface $repository) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $style->section('Archiviere alle Chats');

        $style->caution('Diese Aktion kann nur durch die einzelnen Benutzer rückgängig gemacht werden.');
        if($style->confirm('Sollen wirklich alle Chats archiviert werden?', false) !== true) {
            $style->warning('Abgebrochen, keine Chats archiviert.');
            return Command::SUCCESS;
        }

        $this->repository->archiveAll();
        $style->success('Alle Chats archiviert');

        return Command::SUCCESS;
    }
}