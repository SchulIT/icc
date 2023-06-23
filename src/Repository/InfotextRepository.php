<?php

namespace App\Repository;

use App\Entity\Absence;
use App\Entity\Infotext;
use DateTime;

class InfotextRepository extends AbstractTransactionalRepository implements InfotextRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(Infotext::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function findAllByDate(DateTime $dateTime): array {
        return $this->em
            ->getRepository(Infotext::class)
            ->findBy([
                'date' => $dateTime
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(Infotext $infotext): void {
        $this->em->persist($infotext);
        $this->flushIfNotInTransaction();
    }

    public function removeAll(?DateTime $dateTime = null): void {
        $qb = $this->em->createQueryBuilder()
            ->delete(Infotext::class, 'i');

        if($dateTime !== null) {
            $qb->where('i.date = :date')
                ->setParameter('date', $dateTime);
        }

        $qb->getQuery()
            ->execute();

        $this->flushIfNotInTransaction();
    }

    public function removeBetween(DateTime $start, DateTime $end): int {
        return $this->em->createQueryBuilder()
            ->delete(Infotext::class, 'i')
            ->where('i.date >= :start')
            ->andWhere('i.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();
    }
}