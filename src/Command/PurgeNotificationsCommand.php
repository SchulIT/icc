<?php

namespace App\Command;

use App\Chat\Cleaner;
use App\Repository\NotificationRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:notifications:purge', description: 'Löscht alle Benachrichtigungen.')]
readonly class PurgeNotificationsCommand {
    public function __construct(private NotificationRepositoryInterface $repository) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $style->section('Lösche alle Benachrichtigungen');

        $style->caution('Diese Aktion kann nicht rückgängig gemacht werden');
        if($style->confirm('Sollen wirklich alle Benachrichtigungen gelöscht werden?', false) !== true) {
            $style->warning('Abgebrochen, keine Benachrichtigungen gelöscht.');
            return Command::SUCCESS;
        }

        $this->repository->removeAll();
        $style->success('Alle Benachrichtigungen gelöscht');

        return Command::SUCCESS;
    }
}