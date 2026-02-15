<?php

namespace App\Overtime;

use App\Entity\Appointment;
use App\Entity\ExamSupervision;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;
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