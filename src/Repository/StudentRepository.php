<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\LessonAttendance;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class StudentRepository extends AbstractTransactionalRepository implements StudentRepositoryInterface {

    private function getDefaultQueryBuilder(bool $simple = false): QueryBuilder {
        if($simple === true) {
            return $this->em->createQueryBuilder()
                ->select(['s', 'gm', 'sec'])
                ->from(Student::class, 's')
                ->leftJoin('s.gradeMemberships', 'gm')
                ->leftJoin('s.sections', 'sec');
        }

        return $this->em->createQueryBuilder()
            ->select(['s', 'gm', 'sgm', 'sg', 'sgg', 'sec'])
            ->from(Student::class, 's')
            ->leftJoin('s.gradeMemberships', 'gm')
            ->leftJoin('s.studyGroupMemberships', 'sgm')
            ->leftJoin('sgm.studyGroup', 'sg')
            ->leftJoin('sg.grades', 'sgg')
            ->leftJoin('s.sections', 'sec');
    }

    public function findOneById(int $id): ?Student {
        return $this->getDefaultQueryBuilder()
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByUuid(string $uuid): ?Student {
        return $this->getDefaultQueryBuilder()
            ->andWhere('s.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByExternalId(string $externalId): ?Student {
        return $this->getDefaultQueryBuilder()
            ->andWhere('s.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmailAddress(string $email): ?Student {
        return $this->getDefaultQueryBuilder()
            ->andWhere('s.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade, Section $section): array {
        return $this->getQueryBuilderFindAllByGrade($grade, $section)
            ->getQuery()
            ->getResult();
    }

    private function getQueryBuilderFindAllByGrade(Grade $grade, Section $section): QueryBuilder {
        return $this->getDefaultQueryBuilder(true)
            ->andWhere('gm.grade = :grade')
            ->andWhere('gm.section = :section')
            ->orderBy('s.lastname')
            ->addOrderBy('s.firstname')
            ->setParameter('grade', $grade->getId())
            ->setParameter('section', $section->getId());
    }

    /**
     * @inheritDoc
     */
    public function findAllByQuery(string $query): array {
        $qb = $this->getDefaultQueryBuilder(true);

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(Student::class, 'sInner')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('sInner.firstname', ':query'),
                    $qb->expr()->like('sInner.lastname', ':query')
                )
            );

        $qb
            ->andWhere($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->setParameter('query', '%' . $query . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Student[]
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
        return $this->getDefaultQueryBuilder(true)
            ->andWhere('sec.id = :section')
            ->setParameter('section', $section->getId())
            ->getQuery()
            ->getResult();
    }

    public function persist(Student $student): void {
        $this->em->persist($student);
        $this->flushIfNotInTransaction();
    }

    public function remove(Student $student): void {
        $this->em->remove($student);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(Student::class, 'sInner')
            ->where($qb->expr()->in('sInner.externalId', ':externalIds'));

        $qb
            ->andWhere($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }

    public function findAllByEmailAddresses(array $emailAddresses): array {
        $qb = $this->getDefaultQueryBuilder()
            ->andWhere('s.email IN (:emails)')
            ->setParameter('emails', $emailAddresses);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudyGroups(array $studyGroups): array {
        return $this->getQueryBuilderFindAllByStudyGroups($studyGroups)->getQuery()->getResult();
    }

    public function findAllByBirthday(DateTime $date): array {
        return $this->getDefaultQueryBuilder(true)
            ->where('s.birthday LIKE :date')
            ->setParameter('date', $date->format('%s-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getQueryBuilderFindAllByStudyGroups(array $studyGroups): QueryBuilder {
        $studyGroupIds = array_map(fn(StudyGroup $studyGroup) => $studyGroup->getId(), $studyGroups);

        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(Student::class, 'sInner')
            ->leftJoin('sInner.studyGroupMemberships', 'sgmInner')
            ->where($qb->expr()->in('sgmInner.studyGroup', ':studyGroupIds'));

        $qb
            ->andWhere($qb->expr()->in('s.id', $qbInner->getDQL()))
            ->setParameter('studyGroupIds', $studyGroupIds);

        return $qb;
    }


    /**
     * @inheritDoc
     */
    public function removeOrphaned(): int {
        $qbOrphaned = $this->em->createQueryBuilder()
            ->select('s.id')
            ->from(Student::class, 's')
            ->leftJoin('s.sections', 'sec')
            ->leftJoin('s.gradeMemberships', 'gm')
            ->where('sec.id IS NULL')
            ->orWhere('gm.id IS NULL');

        $qb = $this->em->createQueryBuilder();

        return (int)$qb->delete(Student::class, 'student')
            ->where(
                $qb->expr()->in('student.id', $qbOrphaned->getDQL())
            )
            ->getQuery()
            ->execute();
    }

    public function getStudentsByGradePaginator(int $itemsPerPage, int &$page, Grade $grade, Section $section): Paginator {
        if($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($this->getQueryBuilderFindAllByGrade($grade, $section));
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    public function getStudentsByStudyGroupsPaginator(int $itemsPerPage, int &$page, array $studyGroups): Paginator {
        if($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($this->getQueryBuilderFindAllByStudyGroups($studyGroups)->orderBy('s.lastname')->addOrderBy('s.firstname'));
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

    public function findAllByTuition(Tuition $tuition, array $excludedStatuses = [], bool $includeStudentsWithAttendance = false) {
        $qb = $this->em->createQueryBuilder()
            ->select(['s'])
            ->from(Student::class, 's');

        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->distinct()
            ->from(Student::class, 'sInner')
            ->leftJoin('sInner.studyGroupMemberships', 'sgmInner')
            ->leftJoin('sgmInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.tuitions', 'tInner')
            ->where('tInner.id = :tuition');

        $qb->where(
            $qb->expr()->in('s.id', $qbInner->getDQL())
        )
            ->setParameter('tuition', $tuition->getId());

        if($includeStudentsWithAttendance) {
            $attendanceStudents = $this->em->createQueryBuilder()
                ->select('sAttendance.id')
                ->from(LessonAttendance::class, 'attendanceInner')
                ->leftJoin('attendanceInner.student', 'sAttendance')
                ->leftJoin('attendanceInner.entry', 'entryInner')
                ->leftJoin('entryInner.tuition', 'tuitionInner')
                ->where('tuitionInner.id = :tuition');

            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->in('s.id', $qbInner->getDQL()),
                    $qb->expr()->in('s.id', $attendanceStudents->getDQL())
                )
            )
                ->setParameter('tuition', $tuition->getId());
        }

        if(count($excludedStatuses) > 0) {
            $qb->andWhere(
                $qb->expr()->notIn('s.status', ':excluded')
            )
                ->setParameter('excluded', $excludedStatuses);
        }



        return $qb->getQuery()->getResult();
    }
}