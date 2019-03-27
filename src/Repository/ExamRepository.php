<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Teacher;
use App\Entity\Tuition;
use Doctrine\ORM\EntityManagerInterface;

class ExamRepository implements ExamRepositoryInterface {
    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Exam|null
     */
    public function findOneById(int $id): ?Exam {
        return $this->em->getRepository(Exam::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return Exam|null
     */
    public function findOneByExternalId(string $externalId): ?Exam {
        return $this->em->getRepository(Exam::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuitions(array $tuitions, ?\DateTime $today = null) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->where($qb->expr()->in('tInner.id', ':tuitions'));

        if($today !== null) {
            $qbInner->andWhere(
                'eInner.date >= :today'
            );

            $qb->setParameter('today', $today);
        }

        $qb
            ->select(['e', 'i', 't'])
            ->from(Exam::class, 'e')
            ->leftJoin('e.invigilators', 'i')
            ->leftJoin('e.tuitions', 't')
            ->where($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('tuitions', $tuitions);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTeacher(Teacher $teacher, ?\DateTime $today = null) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.invigilators', 'iInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.additionalTeachers', 'teacherInner')
            ->where(
                $qb->expr()->orX(
                    'teacherInner.id = :teacher',
                    'iInner.teacher = :teacher',
                    'tInner.teacher = :teacher'
                )
            );

        $qb
            ->select(['e', 'i', 't'])
            ->from(Exam::class, 'e')
            ->leftJoin('e.invigilators', 'i')
            ->leftJoin('e.tuitions', 't')
            ->where($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('teacher', $teacher->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime|null $today
     * @return Exam[]
     */
    public function findAll(?\DateTime $today = null) {
        $qb = $this->em->createQueryBuilder()
            ->select(['e', 'i', 't'])
            ->from(Exam::class, 'e')
            ->leftJoin('e.invigilators', 'i')
            ->leftJoin('e.tuitions', 't');

        if($today !== null) {
            $qb->where('e.date > :today')
                ->setParameter('today', $today);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Exam $exam
     */
    public function persist(Exam $exam): void {
        $this->em->persist($exam);
        $this->isTransactionActive || $this->em->flush();
    }

    /**
     * @param Exam $exam
     */
    public function remove(Exam $exam): void {
        $this->em->remove($exam);
        $this->isTransactionActive || $this->em->flush();
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isTransactionActive = true;
    }

    public function commit(): void {
        $this->em->flush();
        $this->em->commit();
        $this->isTransactionActive = false;
    }
}