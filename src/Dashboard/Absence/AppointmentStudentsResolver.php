<?php

namespace App\Dashboard\Absence;

use App\Dashboard\AbsentAppointmentStudent;
use App\Dashboard\AbsentStudent;
use App\Entity\Appointment;
use App\Entity\Student;
use App\Repository\AppointmentRepositoryInterface;
use App\Timetable\TimetableTimeHelper;
use App\Utils\ArrayUtils;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AppointmentStudentsResolver implements AbsenceResolveStrategyInterface {

    private $em;
    private $appointmentRepository;
    private $timeHelper;

    public function __construct(EntityManagerInterface $em, AppointmentRepositoryInterface $appointmentRepository, TimetableTimeHelper $timeHelper) {
        $this->em = $em;
        $this->appointmentRepository = $appointmentRepository;
        $this->timeHelper = $timeHelper;
    }

    /**
     * @inheritDoc
     */
    public function resolveAbsentStudents(DateTime $dateTime, int $lesson, iterable $students): array {
        $students = ArrayUtils::createArrayWithKeys(
            $students,
            function(Student $student) {
                return $student->getId();
            }
        );

        // STEP 1: Resolve student -> appointment relation (IDs only)
        $result = $this->em->createQueryBuilder()
            ->select(['s.id AS studentId', 'a.id AS appointmentId'])
            ->from(Appointment::class, 'a')
            ->leftJoin('a.studyGroups', 'sg')
            ->leftJoin('sg.memberships', 'sgm')
            ->leftJoin('sgm.student', 's')
            ->where('a.start <= :end')
            ->andWhere('a.end >= :start')
            ->andWhere('s.id IN (:students)')
            ->setParameter('start', $this->timeHelper->getLessonStartDateTime($dateTime, $lesson))
            ->setParameter('end', $this->timeHelper->getLessonEndDateTime($dateTime, $lesson))
            ->setParameter('students', array_keys($students))
            ->getQuery()
            ->getScalarResult();

        // STEP 2: Resolve attending appointments
        $appointmentIds = array_unique(
            array_map(function($row) {
                return $row['appointmentId'];
            }, $result)
        );

        $appointments = ArrayUtils::createArrayWithKeys(
            $this->appointmentRepository->findAllByIds($appointmentIds),
            function(Appointment $appointment) {
                return $appointment->getId();
            }
        );

        // STEP 3: compile list of absent students
        $absent = [ ];

        foreach($result as $row) {
            $absent[] = new AbsentAppointmentStudent($students[$row['studentId']], $appointments[$row['appointmentId']]);
        }

        return $absent;
    }
}