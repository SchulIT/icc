<?php

namespace App\Repository;

use App\Entity\Infotext;

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
    public function findAllByDate(\DateTime $dateTime): array {
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

    public function removeAll(): void {
        $this->em->createQueryBuilder()
            ->delete(Infotext::class, 'i')
            ->getQuery()
            ->execute();

        $this->flushIfNotInTransaction();
    }
}