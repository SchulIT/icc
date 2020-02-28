<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class AppointmentRepository extends AbstractTransactionalRepository implements AppointmentRepositoryInterface {
    /**
     * @param int $id
     * @return Appointment|null
     */
    public function findOneById(int $id): ?Appointment {
        return $this->em->getRepository(Appointment::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return Appointment|null
     */
    public function findOneByExternalId(string $externalId): ?Appointment {
        return $this->em->getRepository(Appointment::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @param string|null $idsDQL
     * @param array $parameters
     * @param \DateTime|null $today
     * @return Appointment[]
     */
    private function getAppointments(?string $idsDQL, array $parameters, ?\DateTime $today): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['a', 'c', 'o', 'sg'])
            ->from(Appointment::class, 'a')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.organizers', 'o')
            ->leftJoin('a.studyGroups', 'sg');

        if($idsDQL != null) {
            $qb->where(
                $qb->expr()->in('a.id', $idsDQL)
            );
        }

        foreach($parameters as $key => $value) {
            $qb->setParameter($key, $value);
        }

        if($today !== null) {
            $qb->andWhere('a.start <= :today')
                ->andWhere('a.end >= :today')
                ->setParameter('today', $today);
        }

        $qb->orderBy('a.start', 'asc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Grade $grade
     * @param \DateTime|null $today
     * @param bool $includeHiddenFromStudents
     * @return Appointment[]
     */
    public function findAllForGrade(Grade $grade, ?\DateTime $today = null, bool $includeHiddenFromStudents = false): array {
        $qbStudyGroups = $this->em->createQueryBuilder();

        $qbStudyGroups
            ->select('sgInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.grades', 'sgGradesInner')
            ->where('sgGradesInner.id = :gradeId');

        $qbAppointments = $this->em->createQueryBuilder()
            ->select('aInner.id')
            ->from(Appointment::class, 'aInner')
            ->leftJoin('aInner.studyGroups', 'aSgInner')
            ->where(
                $qbStudyGroups->expr()->in('aSgInner.id', $qbStudyGroups->getDQL())
            );

        if($includeHiddenFromStudents === false) {
            $qbAppointments->andWhere('aInner.isHiddenFromStudents == false');
        }

        return $this->getAppointments($qbAppointments, ['gradeId' => $grade->getId() ], $today);
    }

    /**
     * @param Student[] $students
     * @param \DateTime|null $today
     * @param bool $includeHiddenFromStudents
     * @return Appointment[]
     */
    public function findAllForStudents(array $students, ?\DateTime $today = null, bool $includeHiddenFromStudents = false): array {
        $qbStudyGroups = $this->em->createQueryBuilder();

        $qbStudyGroups
            ->select('sgInner.id')
            ->from(StudyGroup::class, 'sgInner')
            ->leftJoin('sgInner.memberships', 'sgMemberInner')
            ->where('sgMemberInner.student IN (:studentIds)');

        $qbAppointments = $this->em->createQueryBuilder()
            ->select('aInner.id')
            ->from(Appointment::class, 'aInner')
            ->leftJoin('aInner.studyGroups', 'aSgInner')
            ->where(
                $qbStudyGroups->expr()->in('aSgInner.id', $qbStudyGroups->getDQL())
            );

        if($includeHiddenFromStudents === false) {
            $qbAppointments->andWhere('aInner.isHiddenFromStudents == false');
        }

        $studentIds = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        return $this->getAppointments($qbAppointments, ['studentIds' => $studentIds ], $today);
    }

    /**
     * @param Teacher $teacher
     * @param \DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForTeacher(Teacher $teacher, ?\DateTime $today = null): array {
        /**
         * Appointment for teacher means either:
         * - he/she is organizator
         * - he/she teaches a study group
         * - appointment has no study groups associated
         */


    }

    /**
     * @param AppointmentCategory[] $categories
     * @param \DateTime|null $today
     * @return Appointment[]
     */
    public function findAll(array $categories = [ ], ?string $q = null, ?\DateTime $today = null) {
        $qbIds = $this->em->createQueryBuilder();
        $params = [ ];

        $qbIds
            ->select('aInner.id')
            ->from(Appointment::class, 'aInner');

        if(count($categories) > 0) {
            $qbIds
                ->leftJoin('aInner.category', 'cInner')
                ->andWhere('cInner.id IN :categories');

            $params['categories'] = array_map(function(AppointmentCategory $category) {
                return $category->getId();
            }, $categories);
        }

        if($q !== null) {
            $qbIds
                ->andWhere(
                    $qbIds->expr()->orX(
                        'aInner.title LIKE :query',
                        'aInner.content LIKE :query'
                    )
                );

            $params['query'] = '%' . $q . '%';
        }

        return  $this->getAppointments($qbIds->getDQL(), $params, $today);
    }

    /**
     * @param Appointment $appointment
     */
    public function persist(Appointment $appointment): void {
        $this->em->persist($appointment);
        $this->flushIfNotInTransaction();
    }

    /**
     * @param Appointment $appointment
     */
    public function remove(Appointment $appointment): void {
        $this->em->remove($appointment);
        $this->flushIfNotInTransaction();
    }

}