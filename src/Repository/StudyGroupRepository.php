<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;

class StudyGroupRepository extends AbstractTransactionalRepository implements StudyGroupRepositoryInterface {

    /**
     * @param int $id
     * @return StudyGroup|null
     */
    public function findOneById(int $id): ?StudyGroup {
        return $this->em->getRepository(StudyGroup::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByUuid(string $uuid): ?StudyGroup {
        return $this->em->getRepository(StudyGroup::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }

    /**
     * @param string $externalId
     * @return StudyGroup|null
     */
    public function findOneByExternalId(string $externalId, Section $section): ?StudyGroup {
        return $this->em
            ->createQueryBuilder()
            ->select('sg')
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.section', 's')
            ->where('sg.externalId = :id')
            ->andWhere('s.id = :section')
            ->setParameter('id', $externalId)
            ->setParameter('section', $section->getId())
            ->setCacheable(true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByGrade(Grade $grade, Section $section): ?StudyGroup {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('sg')
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g')
            ->leftJoin('sg.section', 's')
            ->where('sg.type = :type')
            ->andWhere('g.id = :grade')
            ->andWhere('s.id = :section')
            ->setParameter('grade', $grade->getId())
            ->setParameter('type', StudyGroupType::Grade())
            ->setParameter('section', $section->getId())
            ->setMaxResults(1)
            ->setFirstResult(0);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByGradeName(string $name, Section $section): ?StudyGroup {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('sg')
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g')
            ->leftJoin('sg.section', 's')
            ->where('sg.type = :type')
            ->andWhere('g.name = :name')
            ->andWhere('s.id = :section')
            ->setParameter('name', $name)
            ->setParameter('type', StudyGroupType::Grade())
            ->setParameter('section', $section->getId())
            ->setMaxResults(1)
            ->setFirstResult(0);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds, Section $section): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('s')
            ->from(StudyGroup::class, 's')
            ->leftJoin('s.section', 'sec')
            ->where($qb->expr()->in('s.externalId', ':externalIds'))
            ->andWhere('sec.id = :section')
            ->setParameter('externalIds', $externalIds)
            ->setParameter('section', $section->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Grade $grade
     * @param StudyGroupType|null $type
     * @return StudyGroup[]
     */
    public function findAllByGrades(Grade $grade, Section $section, ?StudyGroupType $type = null) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sgInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->leftJoin('sgInner.section', 'sInner')
            ->where('gInner.id = :grade')
            ->andWhere('sInner.id = :section');

        if($type !== null) {
            $qbInner
                ->andWhere('sgInner.type = :type');

            $qb->setParameter('type', $type);
        }

        $qb
            ->select(['sg', 'g'])
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g')
            ->where(
                $qb->expr()->in('sg.id', $qbInner->getDQL())
            )
            ->setParameter('grade', $grade->getId())
            ->setParameter('section', $section->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Student $student
     * @return StudyGroup[]
     */
    public function findAllByStudent(Student $student, ?Section $section) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('sgInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.students', 's')
            ->leftJoin('sgInner.section', 'sInner')
            ->where('s.id = :student');

        if($section !== null) {
            $qbInner->andWhere('sInner.id = :section');
        }

        $qb
            ->select(['sg', 'g'])
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g')
            ->where(
                $qb->expr()->in('sg.id', $qbInner->getDQL())
            )
            ->setParameter('student', $student->getId());

        if($section !== null) {
            $qb->setParameter('section', $section->getId());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return StudyGroup[]
     */
    public function findAll() {
        $qb = $this->em->createQueryBuilder()
            ->select(['sg', 'g'])
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySection(Section $section) {
        $qb = $this->em->createQueryBuilder()
            ->select(['sg', 'g'])
            ->from(StudyGroup::class, 'sg')
            ->leftJoin('sg.grades', 'g')
            ->leftJoin('sg.section', 'sec')
            ->where('sec.id = :section')
            ->setParameter('section', $section->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param StudyGroup $studyGroup
     */
    public function persist(StudyGroup $studyGroup): void {
        $this->em->persist($studyGroup);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param StudyGroup $studyGroup
     */
    public function remove(StudyGroup $studyGroup): void {
        $this->em->remove($studyGroup);
        $this->flushIfNotInTransaction();
    }
}