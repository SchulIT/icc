<?php

namespace App\Repository;

use App\Entity\ExcuseNote;
use App\Entity\Student;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ExcuseNoteRepository extends AbstractRepository implements ExcuseNoteRepositoryInterface {

    public function findByStudent(Student $student): array {
        return $this->em->getRepository(ExcuseNote::class)
            ->findBy([
                'student' => $student
            ]);
    }

    public function findByStudentsAndDate(array $students, DateTime $date): array {
        $studentIds = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder();
        $qb->select(['n', 's'])
            ->from(ExcuseNote::class, 'n')
            ->leftJoin('n.student', 's')
            ->where('n.from.date >= :date')
            ->andWhere('n.until.date <= :date')
            ->andWhere(
                $qb->expr()->in('s.id', ':ids')
            )
            ->setParameter('ids', $studentIds)
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    public function persist(ExcuseNote $note): void {
        $this->em->persist($note);
        $this->em->flush();
    }

    public function remove(ExcuseNote $note): void {
        $this->em->remove($note);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function getPaginator(int $itemsPerPage, int &$page, ?Student $student, DateTime $start, DateTime $end): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select(['e', 's'])
            ->from(ExcuseNote::class, 'e')
            ->leftJoin('e.student', 's')
            ->where('e.from.date >= :start')
            ->andWhere('e.until.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('e.from.date', 'desc');

        if($student !== null) {
            $qb->andWhere('s.id = :student')
                ->setParameter('student', $student->getId());
        }

        if($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($qb);
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }
}