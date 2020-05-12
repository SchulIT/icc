<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Tuition;
use Doctrine\ORM\QueryBuilder;

class TuitionRepository extends AbstractTransactionalRepository implements TuitionRepositoryInterface {

    private function getDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['t', 'tt', 'at', 'sg', 's', 'sgs', 'sgss'])
            ->from(Tuition::class, 't')
            ->leftJoin('t.teacher', 'tt')
            ->leftJoin('t.additionalTeachers', 'at')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('sg.memberships', 'sgs')
            ->leftJoin('sgs.student', 'sgss')
            ->leftJoin('t.subject', 's');
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Tuition {
        return $this->getDefaultQueryBuilder()
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?Tuition {
        return $this->getDefaultQueryBuilder()
            ->where('t.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->getDefaultQueryBuilder();
        $qb
            ->where($qb->expr()->in('t.externalId', ':externalIds'))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTeacher(Teacher $teacher) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.additionalTeachers', 'teacherInner')
            ->where(
                $qb->expr()->orX(
                    'teacherInner.id = :teacher',
                    'tInner.teacher = :teacher'
                )
            );

        $qb = $this->getDefaultQueryBuilder()
            ->where($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('teacher', $teacher->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudents(array $students) {
        $studentIds = array_map(function (Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.memberships', 'sInner')
            ->where($qb->expr()->in('sInner.student', ':students'));

        $qb = $this->getDefaultQueryBuilder()
            ->where($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('students', $studentIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrades(array $grades) {
        $gradeIds = array_map(function (Grade $grade) {
            return $grade->getId();
        }, $grades);

        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->where($qb->expr()->in('gInner.id', ':grades'));

        $qb = $this->getDefaultQueryBuilder()
            ->where($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('grades', $gradeIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySubjects(array $subjects) {
        $subjectIds = array_map(function(Subject $subject) {
            return $subject->getId();
        }, $subjects);

        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.subject', 'sInner')
            ->where(
                $qb->expr()->in('sInner.id', ':subjects')
            );

        $qb = $this->getDefaultQueryBuilder()
            ->where($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('subjects', $subjectIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->getDefaultQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function persist(Tuition $tuition): void {
        $this->em->persist($tuition);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function remove(Tuition $tuition): void {
        $this->em->remove($tuition);
        $this->flushIfNotInTransaction();
    }


}