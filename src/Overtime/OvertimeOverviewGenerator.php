<?php

namespace App\Overtime;

use App\Entity\Teacher;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ExamSupervisionRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Sorting\AppointmentStrategy;
use App\Sorting\Sorter;
use App\Sorting\SubstitutionStrategy;
use App\Sorting\TimetableLessonStrategy;
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