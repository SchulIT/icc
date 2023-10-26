<?php

namespace App\Date;

use App\Entity\DateLesson;
use App\Settings\TimetableSettings;

class DateLessonExpander {

    public function __construct(private readonly TimetableSettings $timetableSettings) { }

    /**
     * @param DateLesson $start
     * @param DateLesson $end
     * @return DateLesson[]
     */
    public function expandRangeToDateLessons(DateLesson $start, DateLesson $end): array {
        $current = $start->clone();
        $dateLessons = [ ];

        while($current->getDate()->format('Y-m-d') < $end->getDate()->format('Y-m-d') || ($current->getDate()->format('Y-m-d') === $end->getDate()->format('Y-m-d') && $current->getLesson() <= $end->getLesson())) {
            $dateLessons[] = $current;

            $current = $current->clone();
            if($current->getLesson() === $this->timetableSettings->getMaxLessons()) {
                $current->setLesson(1)->setDate((clone $current->getDate())->modify('+1 day'));
            } else {
                $current->setLesson($current->getLesson() + 1);
            }
        }

        return $dateLessons;
    }
}