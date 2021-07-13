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
            ->where('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('e.date', 'desc');

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