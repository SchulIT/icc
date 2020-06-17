<?php

namespace App\Timetable;

use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetableSupervision;
use App\Entity\TimetableWeek as TimetableWeekEntity;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\Sorter;
use App\Sorting\TimetableWeekStrategy;
use DateTime;
use SchoolIT\CommonBundle\Helper\DateHelper;

/**
 * Helper which transforms a list of TimetableLessons
 * into a Timetable object for easy traversing
 */
class TimetableHelper {

    private $sorter;
    private $dateHelper;
    private $settings;
    private $appointmentRepository;
    private $appointmentCategoryRepository;

    public function __construct(Sorter $sorter, DateHelper $dateHelper, TimetableSettings $settingsManager,
                                AppointmentRepositoryInterface $appointmentRepository, AppointmentCategoryRepositoryInterface $appointmentCategoryRepository) {
        $this->sorter = $sorter;
        $this->dateHelper = $dateHelper;
        $this->settings = $settingsManager;
        $this->appointmentRepository = $appointmentRepository;
        $this->appointmentCategoryRepository = $appointmentCategoryRepository;
    }

    /**
     * @param TimetableWeekEntity[] $weeks
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @return Timetable
     */
    public function makeTimetable(array $weeks, array $lessons, array $supervision = [ ]) {
        $timetable = new Timetable();

        $freeDays = $this->getFreeDays();

        foreach($weeks as $week) {
            $timetable->addWeek(
                $this->makeTimetableWeek($week, count($weeks), $lessons, $supervision, $freeDays)
            );
        }

        $this->addEmptyLessons($timetable);
        $this->collapseTimetable($timetable);
        $this->ensureAllLessonsAreDisplayed($timetable);

        $this->sorter->sort($timetable->getWeeks(), TimetableWeekStrategy::class);
        $numWeeks = count($timetable->getWeeks());

        if($numWeeks > 0){
            $weeks = $timetable->getWeeks();

            for($i = 0; $i < $numWeeks; $i++) {
                /** @var TimetableWeek $first */
                $first = array_shift($weeks);

                if($first->isCurrentOrUpcoming()) {
                    array_unshift($weeks, $first);
                } else {
                    $weeks[] = $first;
                }
            }

            $timetable->setWeeks($weeks);
        }

        return $timetable;
    }

    /**
     * @return DateTime[]
     */
    private function getFreeDays() {
        $categoryIds = $this->settings->getCategoryIds();
        $freeCategories = [ ];

        foreach($this->appointmentCategoryRepository->findAll() as $category) {
            if(in_array($category->getId(), $categoryIds)) {
                $freeCategories[] = $category;
            }
        }

        if(count($freeCategories) === 0) {
            // In case there are no free categories, return fast (findAll([]) will not filter for any category!)
            return [];
        }

        $appointments = $this->appointmentRepository->findAll($freeCategories);

        $freeDays = [ ];

        foreach($appointments as $appointment) {
            if($appointment->isAllDay() === false) {
                continue;
            }

            $date = clone $appointment->getStart();
            while($date < $appointment->getEnd()) {
                $freeDays[] = $date;

                $date = (clone $date)->modify('+1 day');
            }
        }

        return $freeDays;
    }

    /**
     * Ensures that no lessons are missed out even if they are free because otherwise
     * rendering will glitch.
     *
     * @param Timetable $timetable
     */
    private function ensureAllLessonsAreDisplayed(Timetable $timetable): void {
        $numberOfLessons = $this->settings->getMaxLessons();

        foreach($timetable->getWeeks() as $week) {
            $week->setMaxLesson($numberOfLessons);
        }
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
     * @param DateTime[] $freeDays
     * @return TimetableWeek
     */
    private function makeTimetableWeek(TimetableWeekEntity $week, int $numberWeeks, array $lessons, array $supervision, array $freeDays): TimetableWeek {
        $timetableWeek = new TimetableWeek($week);

        $lessons = array_filter($lessons, function(TimetableLessonEntity $lesson) use ($week) {
            return $lesson->getWeek()->getId() === $week->getId();
        });

        $supervision = array_filter($supervision, function(TimetableSupervision $entry) use ($week) {
            return $entry->getWeek()->getId() === $week->getId();
        });

        for($i = 1; $i <= 5; $i++) {
            $isCurrent = $this->isCurrentDay($week, $numberWeeks, $i);
            $isUpcoming = $this->isUpcomingDay($week, $numberWeeks, $i);
            $isFree = $this->isFree($week, $numberWeeks, $i, $freeDays);
            $day = $this->makeTimetableDay($i, $isCurrent, $isUpcoming, $isFree, $lessons, $supervision);

            if($isCurrent || $isUpcoming) {
                $timetableWeek->setCurrentOrUpcoming();
            }

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
     * @param bool $isUpcomingDay
     * @param bool $isFree
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @return TimetableDay
     */
    private function makeTimetableDay(int $day, bool $isCurrentDay, bool $isUpcomingDay, bool $isFree, array $lessons, array $supervision) {
        $timetableDay = new TimetableDay($day, $isCurrentDay, $isUpcomingDay, $isFree);

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

    /**
     * @param TimetableWeekEntity $week
     * @param int $numberWeeks
     * @param int $day
     * @return bool
     */
    private function isUpcomingDay(TimetableWeekEntity $week, int $numberWeeks, int $day) {
        $today = $this->dateHelper->getToday();

        if($this->isWeekend($today) === false) {
            return false;
        }

        while($this->isWeekend($today)) {
            $today = $today->modify('+1 day');
        }

        $weekNumber = (int)$today->format('W');
        $dayNumber = (int)$today->format('w');

        return $weekNumber % $numberWeeks === $week->getWeekMod()
            && $dayNumber === $day;
    }

    /**
     * @param TimetableWeekEntity $week
     * @param int $numberWeeks
     * @param int $day
     * @param DateTime[] $freeDays
     * @return bool
     */
    private function isFree(TimetableWeekEntity $week, int $numberWeeks, int $day, array $freeDays): bool {
        $today = $this->dateHelper->getToday();
        $currentWeekNumber = (int)$today->format('W');

        foreach($freeDays as $freeDay) {
            $weekNumber = (int)$freeDay->format('W');
            $dayNumber = (int)$freeDay->format('w');

            if(($currentWeekNumber === $weekNumber || $currentWeekNumber + 1 === $weekNumber) && $weekNumber % $numberWeeks === $week->getWeekMod() && $dayNumber === $day) {
                return true;
            }
        }

        return false;
    }

    private function isWeekend(\DateTime $dateTime): bool {
        return $dateTime->format('w') == 0 || $dateTime->format('w') == 6;
    }
}