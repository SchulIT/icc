<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\QueryBuilder;

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
            ->orWhere('s.uniqueIdentifier = :externalId')
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
        return $this->getDefaultQueryBuilder(true)
            ->andWhere('gm.grade = :grade')
            ->andWhere('gm.section = :section')
            ->setParameter('grade', $grade->getId())
            ->setParameter('section', $section->getId())
            ->getQuery()
            ->getResult();
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
            ->where($qb->expr()->in('sInner.externalId', ':externalIds'))
            ->orWhere($qb->expr()->in('sInner.uniqueIdentifier', ':externalIds'));

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
}