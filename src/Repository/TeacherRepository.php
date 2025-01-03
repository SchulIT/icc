<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TeacherTag;
use DateTime;
use Doctrine\ORM\QueryBuilder;

class TeacherRepository extends AbstractTransactionalRepository implements TeacherRepositoryInterface {

    private function createDefaultQueryBuilder(): QueryBuilder {
        $qb = $this->em->createQueryBuilder()
            ->select(['t', 's', 'g', 'tt'])
            ->from(Teacher::class, 't')
            ->leftJoin('t.subjects', 's')
            ->leftJoin('t.grades', 'g')
            ->leftJoin('t.tags', 'tt')
            ->leftJoin('t.sections', 'sec')
            ->orderBy('t.acronym', 'asc');

        return $qb;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Teacher {
        $qb = $this->createDefaultQueryBuilder();

        $qb->where('t.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByUuid(string $uuid): ?Teacher {
        $qb = $this->createDefaultQueryBuilder();

        $qb->where('t.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByAcronym(string $acronym): ?Teacher {
        $qb = $this->createDefaultQueryBuilder();

        $qb->where('t.acronym = :acronym')
            ->setParameter('acronym', $acronym)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?Teacher {
        $qb = $this->createDefaultQueryBuilder();

        $qb->where('t.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->setMaxResults(1) ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneByEmailAddress(string $email): ?Teacher {
        $qb = $this->createDefaultQueryBuilder();

        $qb->where('t.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1) ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByAcronym(array $acronyms): array {
        $qb = $this->createDefaultQueryBuilder();

        $qb
            ->where($qb->expr()->in('t.acronym', ':acronyms'))
            ->setParameter('acronyms', $acronyms);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->createDefaultQueryBuilder();

        $qb
            ->where($qb->expr()->in('t.externalId', ':externalIds'))
            ->setParameter('externalIds', $externalIds);

        return $qb->getQuery()->getResult();
    }

    public function findAllByBirthday(DateTime $date): array {
        return $this->createDefaultQueryBuilder()
            ->where('t.birthday LIKE :date')
            ->andWhere('t.showBirthday = true')
            ->setParameter('date', $date->format('%-m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->createDefaultQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySection(Section $section): array {
        return $this->createDefaultQueryBuilder()
            ->andWhere('sec.id = :section')
            ->setParameter('section', $section->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function persist(Teacher $teacher): void {
        $this->em->persist($teacher);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function remove(Teacher $teacher): void {
        $this->em->remove($teacher);
        $this->flushIfNotInTransaction();
    }

    /**
     * @inheritDoc
     */
    public function findAllBySubjectAndTag(?Subject $subject, ?TeacherTag $tag): array {
        $qb = $this->createDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('tInner.id')
            ->from(Teacher::class, 'tInner')
            ->leftJoin('tInner.subjects', 'sInner')
            ->leftJoin('tInner.tags', 'tagsInner');

        if($subject !== null) {
            $qbInner
                ->andWhere('sInner.abbreviation = :subject');
            $qb->setParameter('subject', $subject->getAbbreviation());
        }

        if($tag !== null && $tag->getId() !== null) {
            $qbInner
                ->andWhere('tagsInner.id = :tag');
            $qb->setParameter('tag', $tag->getId());
        }

        $qb->where($qb->expr()->in('t.id', $qbInner->getDQL()));

        return $qb->getQuery()->getResult();
    }
}