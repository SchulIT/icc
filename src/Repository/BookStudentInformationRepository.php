<?php

namespace App\Repository;

use App\Entity\BookStudentInformation;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use DateTime;
use Doctrine\ORM\QueryBuilder;

class BookStudentInformationRepository extends AbstractRepository implements BookStudentInformationRepositoryInterface {

    private function getDefaultQueryBuilder(DateTime|null $from = null, DateTime|null $until = null): QueryBuilder {
        $qb = $this->em->createQueryBuilder();
        $qb->select(['s', 'i'])
            ->from(BookStudentInformation::class, 'i')
            ->leftJoin('i.student', 's');

        if($from !== null && $from == $until) {
            $qb->where('i.from <= :date')
                ->andWhere('i.until >= :date ')
                ->setParameter('date', $from);
        } else if($until !== null && $from !== null) {
            $qb->where('i.from <= :until')
                ->setParameter('until', $until)
                ->andWhere('i.until >= :from')
                ->setParameter('from', $from);
        } else if ($from !== null) {
            $qb->andWhere('i.from <= :from')
                ->setParameter('from', $from);
        }
        else if ($until !== null) {
            $qb->andWhere('i.until >= :until')
                ->setParameter('until', $until);
        }

        return $qb;
    }

    public function findByStudents(array $students, DateTime|null $from = null, DateTime|null $until = null): array {
        $ids = array_map(fn(Student $student) => $student->getId(), $students);

        $qb = $this->getDefaultQueryBuilder($from, $until);
        $qb->andWhere('s.id IN(:students)')
            ->setParameter('students', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findByGrade(Grade $grade, Section $section, DateTime|null $from = null, DateTime|null $until = null): array {
        $qb = $this->getDefaultQueryBuilder($from, $until);

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(Student::class, 'sInner')
            ->leftJoin('sInner.gradeMemberships', 'sgmInner')
            ->leftJoin('sgmInner.grade', 'gInner')
            ->leftJoin('sgmInner.section', 'secInner')
            ->where('gInner.id = :grade')
            ->andWhere('secInner.id = :section');

        $qb->andWhere($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->setParameter('section', $section->getId())
            ->setParameter('grade', $grade->getId());

        return $qb->getQuery()->getResult();
    }

    public function findByStudyGroup(StudyGroup $studyGroup, DateTime|null $from = null, DateTime|null $until = null): array {
        $qb = $this->getDefaultQueryBuilder($from, $until);

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.memberships', 'sgmInner')
            ->leftJoin('sgmInner.student', 'sInner')
            ->where('sgInner.id = :id');

        $qb->andWhere($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->setParameter('id', $studyGroup->getId());

        return $qb->getQuery()->getResult();
    }

    public function removeExpired(DateTime $dateTime): int {
        return $this->em->createQueryBuilder()
            ->delete(BookStudentInformation::class, 'i')
            ->where('i.until <= :date')
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->execute();
    }

    public function persist(BookStudentInformation $information): void {
        $this->em->persist($information);
        $this->em->flush();
    }

    public function remove(BookStudentInformation $information): void {
        $this->em->remove($information);
        $this->em->flush();
    }
}