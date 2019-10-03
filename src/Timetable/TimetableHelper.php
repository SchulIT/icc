<?php

namespace App\Timetable;

use App\Entity\TimetableSupervision;
use App\Settings\TimetableSettings;
use SchoolIT\CommonBundle\Helper\DateHelper;
use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetableWeek as TimetableWeekEntity;

/**
 * Helper which transforms a list of TimetableLessons
 * into a Timetable object for easy traversing
 */
class TimetableHelper {
    private $dateHelper;
    private $settings;

    /**
     * @param DateHelper $dateHelper
     * @param TimetableSettings $settingsManager
     */
    public function __construct(DateHelper $dateHelper, TimetableSettings $settingsManager) {
        $this->dateHelper = $dateHelper;
        $this->settings = $settingsManager;
    }

    /**
     * @param TimetableWeekEntity[] $weeks
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @return Timetable
     */
    public function makeTimetable(array $weeks, array $lessons, array $supervision = [ ]) {
        $timetable = new Timetable();

        foreach($weeks as $week) {
            $timetable->addWeek(
                $this->makeTimetableWeek($week, count($weeks), $lessons, $supervision)
            );
        }

        $this->addEmptyLessons($timetable);
        $this->collapseTimetable($timetable);

        return $timetable;
    }

    /**
     * Adds empty TimetableLessons in order to improve collapsing capabilitites
     *
     * @param Timetable $timetable
     */
    private function addEmptyLessons(Timetable $timetable) {
        foreach($timetable->getWeeks() as $week) {
            $maxLessons = $week->getMaxLessons();

            foreach($week->days as $day) {
                $lessons = $day->getLessons();

                for($lesson = 1; $lesson <= $maxLessons; $lesson++) {
                    if(array_key_exists($lesson, $lessons) !== true) {
                        $day->addEmptyTimetableLesson($lesson);
                    }
                }
            }
        }
    }

    /**
     * Computes the model for double lessons such that the model knows which lessons are collapsed. (Does NOT compute
     * which lessons are considered double lessons -> this information must be set at import)
     *
     * @param Timetable $timetable
     */
    private function collapseTimetable(Timetable $timetable) {
        foreach($timetable->getWeeks() as $week) {
            foreach($week->days as $day) {
                for($lessonNumber = 1; $lessonNumber <= count($day->getLessons()); $lessonNumber++) {
                    if($this->settings->isCollapsible($lessonNumber + 1) !== true) {
                        continue;
                    }

                    $currentLesson = $day->getTimetableLesson($lessonNumber);
                    $nextLesson = $day->getTimetableLesson($lessonNumber + 1);

                    if($currentLesson->isCollapsed()) {
                        continue;
                    }

                    /*
                     * Only collapse if all lessons in the current lesson are double lessons
                     * AND if all lessons in the next lesson do not match the lesson number
                     * as double lessons are added to both lessons
                     */
                    $collapse = true;

                    foreach($currentLesson->getLessons() as $lesson) {
                        if($lesson->isDoubleLesson() === false) {
                            $collapse = false;
                        }
                    }

                    foreach($nextLesson->getLessons() as $lesson) {
                        if($lesson->getLesson() === $nextLesson->getLesson()) {
                            $collapse = false;
                        }
                    }

                    if($collapse === true) {
                        $currentLesson->setIncludeNextLesson();
                        $nextLesson->setCollapsed();
                    }
                }
            }
        }
    }

    /**
     * @param TimetableWeekEntity $week
     * @param int $numberWeeks
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @return TimetableWeek
     */
    private function makeTimetableWeek(TimetableWeekEntity $week, int $numberWeeks, array $lessons, array $supervision): TimetableWeek {
        $timetableWeek = new TimetableWeek($week->getDisplayName(), $week->getWeekMod());

        $lessons = array_filter($lessons, function(TimetableLessonEntity $lesson) use ($week) {
            return $lesson->getWeek()->getId() === $week->getId();
        });

        $supervision = array_filter($supervision, function(TimetableSupervision $entry) use ($week) {
            return $entry->getWeek()->getId() === $week->getId();
        });

        for($i = 1; $i <= 5; $i++) {
            $day = $this->makeTimetableDay($i, $this->isCurrentDay($week, $numberWeeks, $i), $lessons, $supervision);
            $timetableWeek->days[$i] = $day;
        }

        // Calculate max day lessons
        $max = 0;
        foreach($timetableWeek->days as $day) {
            $lessons = array_keys($day->getLessons());
            if(count($lessons) > 0) {
                // max() only works with non-empty arrays
                $max = max($max, max($lessons));
            }
        }

        $timetableWeek->setMaxLesson($max);

        return $timetableWeek;
    }

    /**
     * @param int $day
     * @param bool $isCurrentDay
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @return TimetableDay
     */
    private function makeTimetableDay(int $day, bool $isCurrentDay, array $lessons, array $supervision) {
        $timetableDay = new TimetableDay($day, $isCurrentDay);

        /** @var TimetableLessonEntity[] $lessons */
        $lessons = array_filter($lessons, function(TimetableLessonEntity $lesson) use ($day) {
            return $lesson->getDay() === (int)$day;
        });

        $supervision = array_filter($supervision, function(TimetableSupervision $entry) use($day) {
            return $entry->getDay() === (int)$day;
        });

        foreach($lessons as $lesson) {
            $timetableDay->addTimeTableLesson($lesson);
        }

        foreach($supervision as $entry) {
            $timetableDay->addSupervisionEntry($entry);
        }

        return $timetableDay;
    }

    /**
     * @param TimetableWeekEntity $week
     * @param int $numberWeeks
     * @param int $day
     * @return bool
     */
    private function isCurrentDay(TimetableWeekEntity $week, int $numberWeeks, int $day) {
        $today = $this->dateHelper->getToday();

        $weekNumber = (int)$today->format('W');
        $dayNumber = (int)$today->format('w');

        return $weekNumber % $numberWeeks === $week->getWeekMod()
            && $dayNumber === $day;
    }
}