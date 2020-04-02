<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Teacher;
use Doctrine\ORM\QueryBuilder;

class ExamRepository extends AbstractTransactionalRepository implements ExamRepositoryInterface {

    private function getDefaultQueryBuilder(\DateTime $today = null, bool $onlyToday = false): QueryBuilder {
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
            $qb->setParameter('today', $today);

            if($onlyToday === true) {
                $qb->where('e.date = :today');
            } else {
                $qb->where('e.date > :today');
            }
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
    public function findAllByTeacher(Teacher $teacher, ?\DateTime $today = null, bool $onlyToday = false) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday);

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.invigilators', 'iInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.additionalTeachers', 'teacherInner')
            ->andWhere(
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
    public function findAllByStudents(array $students, ?\DateTime $today = null, bool $onlyToday = false) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday);

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
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade, ?\DateTime $today = null, bool $onlyToday = false) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday);

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner')
            ->where('gInner.id = :grade');

        $qb
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('grade', $grade->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByDateAndLesson(\DateTime $today, int $lesson): array {
        $qb = $this->getDefaultQueryBuilder($today, true);

        $qb
            ->andWhere('e.lessonStart <= :lesson AND e.lessonEnd >= :lesson')
            ->setParameter('lesson', $lesson);

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
     * @inheritDoc
     */
    public function findAllExternal(\DateTime $today = null) {
        $qb = $this->getDefaultQueryBuilder($today);

        return $qb->andWhere($qb->expr()->isNotNull('e.externalId'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Exam $exam
     */
    public function persist(Exam $exam): void {
        $this->em->persist($exam);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param Exam $exam
     */
    public function remove(Exam $exam): void {
        $this->em->remove($exam);
        $this->flushIfNotInTransaction();
    }
}