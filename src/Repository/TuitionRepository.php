<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Entity\Tuition;
use Doctrine\ORM\QueryBuilder;

class TuitionRepository extends AbstractTransactionalRepository implements TuitionRepositoryInterface {

    private function getDefaultQueryBuilder($lazy = false): QueryBuilder {
        if($lazy === true) {
            return $this->em->createQueryBuilder()
                ->select(['t', 'sg', 's', 'sec', 'g'])
                ->from(Tuition::class, 't')
                ->leftJoin('t.studyGroup', 'sg')
                ->leftJoin('sg.grades', 'g')
                ->leftJoin('t.subject', 's')
                ->leftJoin('t.section', 'sec');
        }

        return $this->em->createQueryBuilder()
            ->select(['t', 'tt', 'sg', 's', 'sgs', 'sgss'])
            ->from(Tuition::class, 't')
            ->leftJoin('t.teachers', 'tt')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('sg.memberships', 'sgs')
            ->leftJoin('sgs.student', 'sgss')
            ->leftJoin('t.subject', 's')
            ->leftJoin('t.section', 'sec');
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
    public function findOneByUuid(string $uuid): ?Tuition {
        return $this->getDefaultQueryBuilder()
            ->where('t.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId, Section $section): ?Tuition {
        return $this->filterSection(
                $this->getDefaultQueryBuilder(),
                $section
            )
            ->where('t.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds, Section $section): array {
        $qb = $this->getDefaultQueryBuilder();
        $qb = $this->filterSection($qb, $section);
        $qb
            ->where($qb->expr()->in('t.externalId', ':externalIds'))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTeacher(Teacher $teacher, Section $section): array {
        $qb = $this->getDefaultQueryBuilder();
        $qb = $this->filterSection($qb, $section);

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.teachers', 'teacherInner')
            ->where(
                'teacherInner.id = :teacher'
            );

        $qb->andWhere($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('teacher', $teacher->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudents(array $students, Section $section): array {
        $studentIds = array_map(function (Student $student) {
            return $student->getId();
        }, $students);

        $qb = $this->em->createQueryBuilder();
        $qb = $this->filterSection($qb, $section);

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
    public function findAllByGrades(array $grades): array {
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
    public function findAllBySubjects(array $subjects): array {
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

    public function findAllByGradeAndSubjectOrCourseWithoutTeacher(array $grades, string $subjectOrCourse, Section $section): array {
        $qb = $this->getDefaultQueryBuilder();
        $qb = $this->filterSection($qb, $section);

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.subject', 'sInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->where(
                $qb->expr()->orX(
                    'sInner.abbreviation = :subject',
                    'sgInner.name = :subject'
                )
            );

        $qb->andWhere($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('subject', $subjectOrCourse);

        $tuitions = [ ];
        $result = $qb->getQuery()->getResult();

        /** @var Tuition $tuition */
        foreach($result as $tuition) {
            $tuitionGrades = $tuition->getStudyGroup()->getGrades()->map(function(Grade $grade) { return $grade->getName(); })->toArray();
            if(count(array_intersect($tuitionGrades, $grades)) > 0) {
                $tuitions[] = $tuition;
            }
        }

        return $tuitions;
    }

    /**
     * @inheritDoc
     */
    public function findAllByGradeTeacherAndSubjectOrCourse(array $grades, array $teachers, string $subjectOrCourse, Section $section): array {
        $qb = $this->getDefaultQueryBuilder();
        $qb = $this->filterSection($qb, $section);

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Tuition::class, 'tInner')
            ->leftJoin('tInner.teachers', 'ttInner')
            ->leftJoin('tInner.subject', 'sInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->in('ttInner.acronym', ':teachers'),
                    $qb->expr()->orX(
                        'sInner.abbreviation = :subject',
                        'sgInner.name = :subject'
                    )
                )
            );

        $qb->andWhere($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('teachers', $teachers)
            ->setParameter('subject', $subjectOrCourse);

        $tuitions = [ ];
        $result = $qb->getQuery()->getResult();

        /** @var Tuition $tuition */
        foreach($result as $tuition) {
            $tuitionGrades = $tuition->getStudyGroup()->getGrades()->map(function(Grade $grade) { return $grade->getName(); })->toArray();
            if(count(array_intersect($tuitionGrades, $grades)) > 0) {
                $tuitions[] = $tuition;
            }
        }

        return $tuitions;
    }

    /**
     * @inheritDoc
     */
    public function findOneBySubstitution(Substitution $substitution, Section $section): ?Tuition {
        $grades = array_map(function(Grade $grade) {
            return $grade->getExternalId();
        }, $substitution->getGrades());
        $teachers = array_map(function(Teacher $teacher) {
            return $teacher->getAcronym();
        }, $substitution->getTeachers()->toArray());

        $candidates = $this->findAllByGradeTeacherAndSubjectOrCourse($grades, $teachers, $substitution->getSubject(), $section);

        if(count($candidates) > 0) {
            return $candidates[0];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->getDefaultQueryBuilder(true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySection(Section $section): array {
        return $this->filterSection(
                $this->getDefaultQueryBuilder(true),
                $section
            )
            ->getQuery()
            ->getResult();
    }

    private function filterSection(QueryBuilder $builder, Section $section): QueryBuilder {
        return $builder
            ->andWhere('sec.id = :section')
            ->setParameter('section', $section->getId());
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