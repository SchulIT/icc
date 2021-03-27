<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use DateTime;
use Doctrine\ORM\QueryBuilder;

class StudentRepository extends AbstractTransactionalRepository implements StudentRepositoryInterface {

    private function getDefaultQueryBuilder(bool $simple = false): QueryBuilder {
        if($simple === true) {
            return $this->em->createQueryBuilder()
                ->select(['s', 'g'])
                ->from(Student::class, 's')
                ->leftJoin('s.grade', 'g');
        }

        return $this->em->createQueryBuilder()
            ->select(['s', 'g', 'sgm', 'sg', 'sgg'])
            ->from(Student::class, 's')
            ->leftJoin('s.grade', 'g')
            ->leftJoin('s.studyGroupMemberships', 'sgm')
            ->leftJoin('sgm.studyGroup', 'sg')
            ->leftJoin('sg.grades', 'sgg');
    }

    /**
     * @param int $id
     * @return Student|null
     */
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

    /**
     * @param string $externalId
     * @return Student|null
     */
    public function findOneByExternalId(string $externalId): ?Student {
        return $this->getDefaultQueryBuilder()
            ->andWhere('s.externalId = :externalId')
            ->orWhere('s.uniqueIdentifier = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade): array {
        return $this->getDefaultQueryBuilder(true)
            ->andWhere('g.id = :gradeId')
            ->setParameter('gradeId', $grade->getId())
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
     * @param Student $student
     */
    public function persist(Student $student): void {
        $this->em->persist($student);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param Student $student
     */
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
        $studyGroupIds = array_map(function(StudyGroup $studyGroup) {
            return $studyGroup->getId();
        }, $studyGroups);

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
}