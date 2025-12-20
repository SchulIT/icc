<?php

namespace App\Repository;

use App\Entity\StudentInformation;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudentInformationType;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Override;

class StudentInformationRepository extends AbstractRepository implements StudentInformationRepositoryInterface {

    private function getDefaultQueryBuilder(StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): QueryBuilder {
        $qb = $this->em->createQueryBuilder();
        $qb->select(['s', 'i'])
            ->from(StudentInformation::class, 'i')
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

        if($type !== null) {
            $qb->andWhere('i.type = :type')
                ->setParameter('type', $type);
        }

        return $qb;
    }

    #[Override]
    public function countByStudents(array $students, ?StudentInformationType $type, ?DateTime $from = null, ?DateTime $until = null): int {
        $ids = array_map(fn(Student $student) => $student->getId(), $students);

        $qb = $this->getDefaultQueryBuilder($type, $from, $until);
        $qb->select('COUNT(DISTINCT i.id)');
        $qb->andWhere('s.id IN(:students)')
            ->setParameter('students', $ids);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByStudents(array $students, StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): array {
        $ids = array_map(fn(Student $student) => $student->getId(), $students);

        $qb = $this->getDefaultQueryBuilder($type, $from, $until);
        $qb->andWhere('s.id IN(:students)')
            ->setParameter('students', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findByGrade(Grade $grade, Section $section, StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): array {
        $qb = $this->getDefaultQueryBuilder($type, $from, $until);

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

    public function findByStudyGroup(StudyGroup $studyGroup, StudentInformationType|null $type, DateTime|null $from = null, DateTime|null $until = null): array {
        $qb = $this->getDefaultQueryBuilder($type, $from, $until);

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
            ->delete(StudentInformation::class, 'i')
            ->where('i.until <= :date')
            ->setParameter('date', $dateTime)
            ->getQuery()
            ->execute();
    }

    public function persist(StudentInformation $information): void {
        $this->em->persist($information);
        $this->em->flush();
    }

    public function remove(StudentInformation $information): void {
        $this->em->remove($information);
        $this->em->flush();
    }
}