<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\UserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
     * @param DateTime|null $today
     * @return QueryBuilder
     */
    private function getAppointments(?string $idsDQL, array $parameters, ?DateTime $today): QueryBuilder {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['a', 'c', 'o', 'sg'])
            ->from(Appointment::class, 'a')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.organizers', 'o')
            ->leftJoin('a.studyGroups', 'sg')
            ->leftJoin('a.visibilities', 'v');

        if($idsDQL != null) {
            $qb->where(
                $qb->expr()->in('a.id', $idsDQL)
            );
        }

        foreach($parameters as $key => $value) {
            $qb->setParameter($key, $value);
        }

        if($today !== null) {
            $start = clone $today;
            $end = clone $today;
            $end->modify('+1 day');
            $start->modify('+1 second');

            $qb->andWhere(
                $qb->expr()->orX(
                // appointments starts today
                    $qb->expr()->andX(
                        'a.start >= :start',
                        'a.start < :end'
                    ),

                    // appointment is on
                    $qb->expr()->andX(
                        'a.start <= :start',
                        'a.end >= :end'
                    ),

                    // last day of appointment
                    $qb->expr()->andX(
                        'a.end >= :start',
                        'a.end < :end'
                    )
                )
            )
                ->setParameter('start', $start)
                ->setParameter('end', $end);
        }

        $qb->orderBy('a.start', 'asc');

        return $qb;
    }

    /**
     * @inheritDoc
     */
    public function findAllForStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null): array {
        $qbAppointments = $this->em->createQueryBuilder()
            ->select('aInner.id')
            ->from(Appointment::class, 'aInner')
            ->leftJoin('aInner.studyGroups', 'aSgInner')
            ->where('aSgInner.id = :studyGroupId');

        return $this->getAppointments($qbAppointments, ['studyGroupId' => $studyGroup->getId() ], $today)
            ->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllForStudents(array $students, ?DateTime $today = null): array {
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

        $studentIds = array_map(function(Student $student) {
            return $student->getId();
        }, $students);

        return $this->getAppointments($qbAppointments, ['studentIds' => $studentIds ], $today)
            ->getQuery()->getResult();
    }

    /**
     * @param Teacher $teacher
     * @param DateTime|null $today
     * @return Appointment[]
     */
    public function findAllForTeacher(Teacher $teacher, ?DateTime $today = null): array {
        /**
         * Appointment for teacher means either:
         * - he/she is organizer (1)
         * - appointment has visibility "teachers" (2)
         */

        // Query (1)
        $qbTeacherOrganizer = $this->em->createQueryBuilder()
            ->select('aTOInner.id')
            ->from(Appointment::class, 'aTOInner')
            ->leftJoin('aTOInner.organizers', 'oTOInner')
            ->where('oTOInner.id = :teacherId');

        // Query (2)
        $qbTeacherAppointments = $this->em->createQueryBuilder();
        $qbTeacherAppointments
            ->select('aTAInner.id')
            ->from(Appointment::class, 'aTAInner')
            ->leftJoin('aTAInner.visibilities', 'vTAInner')
            ->where('vTAInner.userType = :userType');

        // Combine (1) and (2)
        $qbAppointments = $this->em->createQueryBuilder();
        $qbAppointments
            ->select('aInner.id')
            ->from(Appointment::class, 'aInner')
            ->where(
                $qbAppointments->expr()->orX(
                    $qbAppointments->expr()->in('aInner.id', $qbTeacherOrganizer->getDQL()),
                    $qbAppointments->expr()->in('aInner.id', $qbTeacherAppointments->getDQL())
                )
            );

        return $this->getAppointments($qbAppointments, [
            'teacherId' => $teacher->getId(),
            'userType' => UserType::Teacher()
        ], $today)
            ->getQuery()->getResult();
    }

    /**
     * @param AppointmentCategory[] $categories
     * @param DateTime|null $today
     * @return Appointment[]
     */
    public function findAll(array $categories = [ ], ?string $q = null, ?DateTime $today = null) {
        $qbIds = $this->em->createQueryBuilder();
        $params = [ ];

        $qbIds
            ->select('aInner.id')
            ->from(Appointment::class, 'aInner');

        if(count($categories) > 0) {
            $qbIds
                ->leftJoin('aInner.category', 'cInner')
                ->andWhere('cInner.id IN (:categories)');

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

        return $this->getAppointments($qbIds->getDQL(), $params, $today)
            ->getQuery()->getResult();
    }

    public function getPaginator(int $itemsPerPage, int &$page, array $categories = [ ], ?string $q = null): Paginator {
        $qbIds = $this->em->createQueryBuilder();
        $params = [ ];

        $qbIds
            ->select('aInner.id')
            ->from(Appointment::class, 'aInner');

        if(count($categories) > 0) {
            $qbIds
                ->leftJoin('aInner.category', 'cInner')
                ->andWhere('cInner.id IN (:categories)');

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

        $qb = $this->getAppointments($qbIds->getDQL(), $params, null);

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