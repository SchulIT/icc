<?php

namespace App\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Entity\IdEntity;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("@daily")
 */
class RemoveOldSamlIdsCommand extends Command {
    private EntityManagerInterface $em;
    private const Days = 30;

    public function __construct(EntityManagerInterface $em, string $name = null) {
        parent::__construct($name);
        $this->em = $em;
    }

    public function configure() {
        $this->setName('app:saml:remove_ids')
            ->setDescription('Removes old SAML _InResponse IDs.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);
        $threshold = (new DateTime('today'))->modify(sprintf('-%d days', self::Days));

        $style->section(sprintf('Remove _InResponse IDs older than %s', $threshold->format('c')));

        $count = $this->em->createQueryBuilder()
            ->delete(IdEntity::class, 'i')
            ->where('i.expiry < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();

        $style->success(sprintf('Removed %d _InResponse IDs.', $count));

        return 0;
    }
}