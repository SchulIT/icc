<?php

namespace App\Overtime;

use App\Appointment\Entity\Appointment;
use App\Exam\Entity\ExamSupervision;
use App\Substitution\Entity\Substitution;
use App\Timetable\Entity\TimetableLesson;
use DateTime;

readonly class Day {

    /**
     * @param DateTime $date
     * @param TimetableLesson[] $timetable
     * @param Substitution[] $substitutions
     * @param ExamSupervision[] $examSupervisions
     * @param Appointment[] $appointments
     */
    public function __construct(
        public DateTime $date,
        public array $timetable,
        public array $substitutions,
        public array $examSupervisions,
        public array $appointments
    ) {

    }
}