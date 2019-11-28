<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\Substitution;
use App\Entity\Teacher;
use Doctrine\ORM\QueryBuilder;

class SubstitutionRepository extends AbstractTransactionalRepository implements SubstitutionRepositoryInterface {

    /**
     * @param int $id
     * @return Substitution|null
     */
    public function findOneById(int $id): ?Substitution {
        return $this->em->getRepository(Substitution::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return Substitution|null
     */
    public function findOneByExternalId(string $externalId): ?Substitution {
        return $this->em->getRepository(Substitution::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(Substitution::class)
            ->findAll();
    }

    /**
     * @param \DateTime $date
     * @return Substitution[]
     */
    public function findAllByDate(\DateTime $date) {
        return $this->em->getRepository(Substitution::class)
            ->findBy([
                'date' => $date
            ]);
    }

    /**
     * @param Substitution $substitution
     */
    public function persist(Substitution $substitution): void {
        $this->em->persist($substitution);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param Substitution $substitution
     */
    public function remove(Substitution $substitution): void {
        $this->em->remove($substitution);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function findAllForStudyGroups(array $studyGroups, ?\DateTime $date = null) {
        $ids = array_map(function(StudyGroup $studyGroup) {
            return $studyGroup->getId();
        }, $studyGroups);

        $qbInner = $this->em->createQueryBuilder();
        $qbInner->select('sInner.id')
            ->from(Substitution::class, 'sInner')
            ->leftJoin('sInner.studyGroups', 'sgInner')
            ->leftJoin('sInner.replacementStudyGroups', 'rsgInner');

        $qbInner->where(
            $qbInner->expr()->orX(
                $qbInner->expr()->in('sgInner.id', ':ids'),
                $qbInner->expr()->in('rsgInner.id', ':ids')
            )
        );

        $qb = $this->getDefaultQueryBuilder();
        $qb->where(
            $qb->expr()->in('s.id', $qbInner->getDQL())
        );
        $qb->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllForTeacher(Teacher $teacher, ?\DateTime $date = null) {
        $qbInner = $this->em->createQueryBuilder();
        $qbInner->select('sInner.id')
            ->from(Substitution::class, 'sInner')
            ->leftJoin('sInner.teacher', 'tInner')
            ->leftJoin('sInner.replacementTeacher', 'rtInner');

        $qbInner->where(
            $qbInner->expr()->orX(
                'tInner.id = :id',
                'rtInner.id = :id',
                'sInner.remark LIKE :acronymQuery',
                'sInner.remark LIKE :nameQuery'
            )
        );

        $qb = $this->getDefaultQueryBuilder();
        $qb->where(
            $qb->expr()->in('s.id', $qbInner->getDQL())
        );
        $qb->setParameter('id', $teacher->getId());
        $qb->setParameter('acronymQuery', '%' . $teacher->getAcronym() . '%');
        $qb->setParameter('nameQuery', '%' . $teacher->getLastname() . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllForGrade(Grade $grade, ?\DateTime $date = null) {
        $qbInner = $this->em->createQueryBuilder();
        $qbInner->select('sInner.id')
            ->from(Substitution::class, 'sInner')
            ->leftJoin('sInner.studyGroups', 'sgInner')
            ->leftJoin('sInner.replacementStudyGroups', 'rsgInner')
            ->leftJoin('sgInner.grades', 'sggInner')
            ->leftJoin('rsgInner.grades', 'rsggInner');

        $qbInner->where(
            $qbInner->expr()->orX(
                $qbInner->expr()->eq('sggInner.id', ':id'),
                $qbInner->expr()->eq('rsggInner.id', ':id')
            )
        );

        $qb = $this->getDefaultQueryBuilder();
        $qb->where(
            $qb->expr()->in('s.id', $qbInner->getDQL())
        );
        $qb->setParameter('id', $grade->getId());

        return $qb->getQuery()->getResult();
    }

    private function getDefaultQueryBuilder(): QueryBuilder {
        $qb = $this->em->createQueryBuilder();

        $qb->select(['s', 't', 'rt', 'sg', 'rsg'])
            ->from(Substitution::class, 's')
            ->leftJoin('s.teacher', 't')
            ->leftJoin('s.replacementTeacher', 'rt')
            ->leftJoin('s.studyGroups', 'sg')
            ->leftJoin('s.replacementStudyGroups', 'rsg')
            ->orderBy('s.date', 'asc')
            ->orderBy('s.lessonStart', 'asc');

        return $qb;
    }
}