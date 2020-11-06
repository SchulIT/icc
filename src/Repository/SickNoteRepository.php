<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\SickNote;
use App\Entity\Student;
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

    /**
     * @inheritDoc
     */
    public function findByStudents(array $students): array {
        $ids = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where($qb->expr()->in('s.id', ':students'))
            ->setParameter('students', $ids);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findByGrade(Grade $grade): array {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's')
            ->leftJoin('s.grade', 'g')
            ->where('g.id = :grade')
            ->setParameter('grade', $grade->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        return $qb->getQuery()->getResult();
    }
}