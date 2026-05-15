<?php

namespace App\Overtime;

use App\Common\Entity\Teacher;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Exam\Repository\ExamSupervisionRepositoryInterface;
use App\Substitution\Repository\SubstitutionRepositoryInterface;
use App\Timetable\Repository\TimetableLessonRepositoryInterface;
use App\Appointment\Sorting\AppointmentStrategy;
use App\Framework\Sorting\Sorter;
use App\Substitution\Sorting\SubstitutionStrategy;
use App\Timetable\Sorting\TimetableLessonStrategy;
use DateInterval;
use DateTime;
use InvalidArgumentException;

readonly class OvertimeOverviewGenerator {
    public function __construct(
        private SubstitutionRepositoryInterface $substitutionRepository,
        private TimetableLessonRepositoryInterface $timetableLessonRepository,
        private AppointmentRepositoryInterface $appointmentRepository,
        private ExamSupervisionRepositoryInterface $examSupervisionRepository,
        private Sorter $sorter
    ) {

    }

    public function generate(Teacher $teacher, DateTime $start, DateTime $end): OvertimeOverview {
        $days = [ ];

        if($start > $end) {
            throw new InvalidArgumentException('Start date must be after end date');
        }

        $current = clone $start;
        while($current <= $end) {
            $lessons = $this->timetableLessonRepository->findAllByTeacher($current, $current, $teacher);
            $substitutions = $this->substitutionRepository->findAllForTeacher($teacher, $current);
            $examSupervisions = $this->examSupervisionRepository->findByTeacherAndDate($teacher, $current);
            $appointments = $this->appointmentRepository->findAllForTeacher($teacher, $current);

            $this->sorter->sort($lessons, TimetableLessonStrategy::class);
            $this->sorter->sort($substitutions, SubstitutionStrategy::class);
            $this->sorter->sort($appointments, AppointmentStrategy::class);

            $days[] = new Day(
                $current,
                $lessons,
                $substitutions,
                $examSupervisions,
                $appointments
            );

            $current = (clone $current)->add(new DateInterval('P1D'));
        }

        return new OvertimeOverview(
            $days,
            $start,
            $end
        );
    }
}