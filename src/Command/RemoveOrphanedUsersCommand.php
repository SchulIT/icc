<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('@daily')]
#[AsCommand('app:user:remove_orphaned', 'Löscht Lernende, Eltern und Lehrkräfte ohne verknüpfte Entität.')]
readonly class RemoveOrphanedUsersCommand {

    public function __construct(private UserRepositoryInterface $userRepository) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $count = $this->userRepository->removeOrphaned();

        $style->success(sprintf('%d verwaiste Benutzer gelöscht', $count));

        return Command::SUCCESS;
    }
}