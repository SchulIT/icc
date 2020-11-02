<?php

namespace App\Repository;

use App\Entity\SickNote;
use App\Entity\User;
use DateTime;

class SickNoteRepository extends AbstractRepository implements SickNoteRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findByUser(User $user): array {
        return $this->em->getRepository(SickNote::class)
            ->findBy([
                'createdBy' => $user
            ], [
                'createdAt' => 'desc'
            ]);
    }

    public function persist(SickNote $note): void {
        $this->em->persist($note);
        $this->em->flush();
    }

    public function remove(SickNote $note): void {
        $this->em->remove($note);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function removeExpired(DateTime $threshold): int {
        return $this->em->createQueryBuilder()
            ->delete(SickNote::class, 's')
            ->where('s.until < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();
    }
}