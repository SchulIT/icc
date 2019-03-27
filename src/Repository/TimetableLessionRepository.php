<?php

namespace App\Repository;

use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use Doctrine\ORM\EntityManagerInterface;

class TimetableLessionRepository implements TimetableLessonRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?TimetableLesson {
        return $this->em->getRepository(TimetableLesson::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByPeriod(TimetablePeriod $period) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('tlInner.id')
            ->from(TimetableLesson::class, 'tlInner')
            ->leftJoin('tlInner.period', 'tpInner')
            ->where('tpInner.id = :period');

        $qb
            ->select(['l', 'p', 't'])
            ->from(TimetableLesson::class, 'l')
            ->leftJoin('l.period', 'p')
            ->leftJoin('l.tention', 't')
            ->where($qb->expr()->in('l.id', $qbInner->getDQL()))
            ->setParameter('period', $period->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(TimetableLesson::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(TimetableLesson $lesson): void {
        $this->em->persist($lesson);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(TimetableLesson $lesson): void {
        $this->em->remove($lesson);
        $this->em->flush();
    }
}