<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Entity\TeacherAbsence;
use App\Entity\TeacherAbsenceLesson;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

class TeacherAbsenceRepository extends AbstractRepository implements TeacherAbsenceRepositoryInterface {

    public function getPaginator(int $itemsPerPage, int &$page, bool $hideProcessed, ?DateTime $startDate, ?DateTime $endDate, ?Teacher $teacher = null): Paginator {
        $qb = $this->em->createQueryBuilder()
            ->select('a')
            ->from(TeacherAbsence::class, 'a')
            ->orderBy('a.from.date', 'desc');

        if($hideProcessed) {
            $qb->andWhere('a.processedAt IS NULL');
        }

        if($startDate !== null) {
            $qb->andWhere('a.from.date >= :start');
        }

        if($endDate !== null) {
            $qb->andWhere('a.until.date <= :end');
        }

        if($teacher !== null) {
            $qb->andWhere('a.teacher = :teacher')
                ->setParameter('teacher', $teacher);
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

    public function persist(TeacherAbsence|TeacherAbsenceLesson $absenceOrLesson): void {
        $this->em->persist($absenceOrLesson);
        $this->em->flush();
    }

    public function remove(TeacherAbsence|TeacherAbsenceLesson $absenceOrLesson): void {
        $this->em->remove($absenceOrLesson);
        $this->em->flush();
    }
}