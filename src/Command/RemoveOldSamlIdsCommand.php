<?php

namespace App\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Entity\IdEntity;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('@monthly')]
#[AsCommand('app:saml:remove_ids', 'Löscht alle SAML _InResponse IDs.')]
readonly class RemoveOldSamlIdsCommand {
    private const int Days = 30;

    public function __construct(private EntityManagerInterface $em) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $threshold = (new DateTime('today'))->modify(sprintf('-%d days', self::Days));

        $style->section(sprintf('Lösche IDs älter als %s', $threshold->format('c')));

        $count = $this->em->createQueryBuilder()
            ->delete(IdEntity::class, 'i')
            ->where('i.expiry < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();

        $style->success(sprintf('%d _InResponse ID(s) gelöscht', $count));

        return Command::SUCCESS;
    }
}