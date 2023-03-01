<?php

namespace App\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Entity\IdEntity;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob('@monthly')]
#[AsCommand('app:saml:remove_ids', 'Löscht alle SAML _InResponse IDs.')]
class RemoveOldSamlIdsCommand extends Command {
    private const Days = 30;

    public function __construct(private EntityManagerInterface $em, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);
        $threshold = (new DateTime('today'))->modify(sprintf('-%d days', self::Days));

        $style->section(sprintf('Lösche IDs älter als %s', $threshold->format('c')));

        $count = $this->em->createQueryBuilder()
            ->delete(IdEntity::class, 'i')
            ->where('i.expiry < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();

        $style->success(sprintf('%d _InResponse ID(s) gelöscht', $count));

        return 0;
    }
}