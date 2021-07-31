<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ExamRepository extends AbstractTransactionalRepository implements ExamRepositoryInterface {

    private function getDefaultQueryBuilder(\DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true): QueryBuilder {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['e', 's', 't', 'sg', 'g', 'at', 'tt', 'st'])
            ->from(Exam::class, 'e')
            ->leftJoin('e.supervisions', 's')
            ->leftJoin('s.teacher', 'st')
            ->leftJoin('e.tuitions', 't')
            ->leftJoin('t.teachers', 'tt')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('sg.grades', 'g');

        if($today !== null) {
            $qb->setParameter('today', $today);

            if($onlyToday === true) {
                $qb->where('e.date = :today');
            } else {
                $qb->where('e.date >= :today');
            }
        }

        if($onlyPlanned === true) {
            $qb->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->isNotNull('e.date'),
                    $qb->expr()->isNotNull('e.lessonStart'),
                    $qb->expr()->isNotNull('e.lessonEnd')
                )
            );
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
    public function findAllByIds(array $ids): array {
        return $this->getDefaultQueryBuilder()
            ->andWhere('e.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTuitions(array $tuitions, ?\DateTime $today = null, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, false, $onlyPlanned);

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
    public function findAllByStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned);

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sInner')
            ->where('sInner.id = :studyGroup');

        $qb
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('studyGroup', $studyGroup->getId());

        return $qb->getQuery()->getResult();
    }

    public function findAllDatesByStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, false, $onlyPlanned)
            ->select(['e.date', 'COUNT(DISTINCT e.id) AS count'])
            ->groupBy('e.date');

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sInner')
            ->where('sInner.id = :studyGroup');

        $qb
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('studyGroup', $studyGroup->getId());

        return $qb->getQuery()->getScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByTeacher(Teacher $teacher, ?\DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned);

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.supervisions', 'sInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.teachers', 'teacherInner')
            ->andWhere(
                $qb->expr()->orX(
                    'teacherInner.id = :teacher',
                    'sInner.teacher = :teacher'
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
    public function findAllDatesByTeacher(Teacher $teacher, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned)
            ->select(['e.date', 'COUNT(DISTINCT e.id) AS count'])
            ->groupBy('e.date');

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.supervisions', 'sInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.teachers', 'teacherInner')
            ->andWhere(
                $qb->expr()->orX(
                    'teacherInner.id = :teacher',
                    'sInner.teacher = :teacher',
                )
            );

        $qb
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->setParameter('teacher', $teacher->getId());

        return $qb->getQuery()->getScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudents(array $students, ?\DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned);

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
    public function findAllDatesByStudents(array $students, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned)
            ->select(['e.date', 'COUNT(DISTINCT e.id) AS count'])
            ->groupBy('e.date');

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

        return $qb->getQuery()->getScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByGrade(Grade $grade, ?\DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned);

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
    public function findAllDatesByGrade(Grade $grade, ?DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        $qb = $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned)
            ->select(['e.date', 'COUNT(DISTINCT e.id) AS count'])
            ->groupBy('e.date');

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

        return $qb->getQuery()->getScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByDate(DateTime $today): array {
        $qb = $this->getDefaultQueryBuilder($today, true);
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
     * @inheritDoc
     */
    public function findAllByRoomAndDate(Room $room, DateTime $today): array {
        $qb = $this->getDefaultQueryBuilder($today, true);

        $qb
            ->andWhere('e.room = :room')
            ->setParameter('room', $room->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByRoomAndDateAndLesson(Room $room, DateTime $today, int $lesson): array {
        $qb = $this->getDefaultQueryBuilder($today, true);

        $qb
            ->andWhere('e.lessonStart <= :lesson AND e.lessonEnd >= :lesson')
            ->setParameter('lesson', $lesson)
            ->andWhere('e.room = :room')
            ->setParameter('room', $room->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(?\DateTime $today = null, bool $onlyToday = false, bool $onlyPlanned = true) {
        return $this->getDefaultQueryBuilder($today, $onlyToday, $onlyPlanned)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllDates(?\DateTime $today = null, bool $onlyPlanned = true) {
        return $this->getDefaultQueryBuilder($today, false, $onlyPlanned)
            ->select(['e.date', 'COUNT(DISTINCT e.id) AS count'])
            ->groupBy('e.date')
            ->getQuery()
            ->getScalarResult();
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


    /**
     * @inheritDoc
     */
    public function getPaginator(int $itemsPerPage, int &$page, ?Grade $grade = null, ?Teacher $teacher = null, ?Student $student = null, ?StudyGroup $studyGroup = null, bool $onlyPlanned = true, ?DateTime $today = null, ?DateTime $end = null): Paginator {
        $qb = $this->getDefaultQueryBuilder($today, false, $onlyPlanned);

        if($end !== null) {
            $qb->andWhere('e.date <= :end')
                ->setParameter('end', $end);
        }

        $qbInner = $this->em->createQueryBuilder()
            ->select('eInner.id')
            ->from(Exam::class, 'eInner')
            ->leftJoin('eInner.tuitions', 'tInner')
            ->leftJoin('tInner.studyGroup', 'sgInner')
            ->leftJoin('sgInner.grades', 'gInner');

        if($grade !== null) {
            $qbInner->where('gInner.id = :grade');
            $qb->setParameter('grade', $grade->getId());
        }

        if($teacher !== null) {
            $qbInner
                 ->leftJoin('eInner.supervisions', 'sInner')
                 ->leftJoin('tInner.teachers', 'atInner')
                 ->andWhere(
                 $qb->expr()->orX(
                     'tInner.teacher = :teacher',
                     'atInner.id = :teacher'
                 )
             );
             $qb->setParameter('teacher', $teacher->getId());
        }

        if($student !== null) {
            $qbInner
                ->leftJoin('eInner.students', 'sInner')
                ->andWhere('sInner.id = :student');
            $qb->setParameter('student', $student->getId());
        }

        if($studyGroup !== null) {
            $qbInner
                ->andWhere('sgInner.id = :studygroup');
            $qb->setParameter('studygroup', $studyGroup->getId());
        }

        $qb
            ->andWhere($qb->expr()->in('e.id', $qbInner->getDQL()))
            ->orderBy('e.date', 'asc');

        if(!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($qb);
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }

}