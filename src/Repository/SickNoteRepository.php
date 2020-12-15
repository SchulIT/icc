<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\SickNote;
use App\Entity\Student;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\QueryBuilder;

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
    public function findByStudents(array $students, ?DateTime $date = null, ?int $lesson = null): array {
        $ids = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        $qb->where($qb->expr()->in('s.id', ':students'))
            ->setParameter('students', $ids);

        if($date != null && $lesson != null) {
            $qb->andWhere(
                // start
                $qb->expr()->orX(
                    'sn.from.date < :date',
                    $qb->expr()->andX(
                        'sn.from.date = :date',
                        'sn.from.lesson <= :lesson'
                    )
                )
            );
            $qb->andWhere(
                // end
                $qb->expr()->orX(
                    'sn.until.date > :date',
                    $qb->expr()->andX(
                        'sn.until.date = :date',
                        'sn.until.lesson >= :lesson'
                    )
                )
            );
            $qb->setParameter('date', $date);
            $qb->setParameter('lesson', $lesson);
        } if($date !== null) {
            $this->applyDate($qb, $date);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findByGrade(Grade $grade, ?DateTime $date = null): array {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's')
            ->leftJoin('s.grade', 'g')
            ->where('g.id = :grade')
            ->setParameter('grade', $grade->getId());

        if($date !== null) {
            $this->applyDate($qb, $date);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(?DateTime $date = null): array {
        $qb = $this->em->createQueryBuilder()
            ->select('sn', 's')
            ->from(SickNote::class, 'sn')
            ->leftJoin('sn.student', 's');

        if($date !== null) {
            $this->applyDate($qb, $date);
        }

        return $qb->getQuery()->getResult();
    }

    private function applyDate(QueryBuilder $qb, DateTime $date) {
        $qb->andWhere('sn.from.date <= :date');
        $qb->andWhere('sn.until.date >= :date');
        $qb->setParameter('date', $date);
    }
}