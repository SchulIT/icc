<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class ExamRepository implements ExamRepositoryInterface {
    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    private function getDefaultQueryBuilder(\DateTime $today = null): QueryBuilder {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['e', 'i', 's', 't', 'sg', 'g', 'at', 'tt', 'it'])
            ->from(Exam::class, 'e')
            ->leftJoin('e.invigilators', 'i')
            ->leftJoin('i.teacher', 'it')
            ->leftJoin('e.students', 's')
            ->leftJoin('e.tuitions', 't')
            ->leftJoin('t.teacher', 'tt')
            ->leftJoin('t.additionalTeachers', 'at')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('sg.grades', 'g');

        if($today !== null) {
            $qb->where('e.date > :today')
                ->setParameter('today', $today);
        }

        return $qb;
    }

    /**
     * @param int $id
     * @return Exam|null
     */
    public function findOneById(int $id): ?Exam {
        return $this->getDefaultQueryBuilder()
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $externalId
     * @return Exam|null
     */
    public function findOneByExternalId(string $externalId): ?Exam {
        return $this->getDefaultQueryBuilder()
            ->andWhere('e.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuitions(array $tuitions, ?\DateTime $today = null) {
        $qb = $this->getDefaultQueryBuilder($today);

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->where($qb->expr()->in('tInner.id', ':tuitions'));

        $qb
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('tuitions', $tuitions);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTeacher(Teacher $teacher, ?\DateTime $today = null) {
        $qb = $this->getDefaultQueryBuilder($today);

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
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('teacher', $teacher->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudents(array $students, ?\DateTime $today = null) {
        $qb = $this->getDefaultQueryBuilder($today);

        $studentIds = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.students', 'sInner')
            ->where(
                $qb->expr()->in('sInner.id', ':studentIds')
            );

        $qb
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('studentIds', $studentIds);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime|null $today
     * @return Exam[]
     */
    public function findAll(?\DateTime $today = null) {
        return $this->getDefaultQueryBuilder($today)
            ->getQuery()
            ->getResult();
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