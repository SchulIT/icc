<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use Doctrine\ORM\EntityManagerInterface;

class TuitionRepository implements TuitionRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Tuition {
        return $this->em->getRepository(Tuition::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?Tuition {
        return $this->em->getRepository(Tuition::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByExternalId(array $externalIds): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('t')
            ->from(Tuition::class, 't')
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

        $qb
            ->select(['t', 'tt', 'at', 'sg', 's'])
            ->from(Tuition::class, 't')
            ->leftJoin('t.teacher', 'tt')
            ->leftJoin('t.additionalTeachers', 'at')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('t.subject', 's')
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
            ->leftJoin('sgInner.students', 'sInner')
            ->where($qb->expr()->in('sInner.id', ':students'));

        $qb
            ->select(['t', 'tt', 'at', 'sg', 's'])
            ->from(Tuition::class, 't')
            ->leftJoin('t.teacher', 'tt')
            ->leftJoin('t.additionalTeachers', 'at')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('t.subject', 's')
            ->where($qb->expr()->in('t.id', $qbInner->getDQL()))
            ->setParameter('students', $studentIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(Tuition::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(Tuition $tuition): void {
        $this->em->persist($tuition);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(Tuition $tuition): void {
        $this->em->remove($tuition);
        $this->em->flush();
    }
}