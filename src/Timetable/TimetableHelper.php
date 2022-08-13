<?php

namespace App\Timetable;

use App\Date\WeekOfYear;
use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetableSupervision;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\FreeTimespanRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Utils\ArrayUtils;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;

/**
 * Helper which transforms a list of TimetableLessons
 * into a Timetable object for easy traversing
 */
class TimetableHelper {

    private DateHelper $dateHelper;
    private TimetableSettings $settings;
    private AppointmentRepositoryInterface $appointmentRepository;
    private AppointmentCategoryRepositoryInterface $appointmentCategoryRepository;
    private FreeTimespanRepositoryInterface $freeTimespanRepository;
    private TimetableWeekRepositoryInterface $weekRepository;

    public function __construct(DateHelper $dateHelper, TimetableSettings $settingsManager,
                                AppointmentRepositoryInterface $appointmentRepository, FreeTimespanRepositoryInterface $freeTimespanRepository,
                                AppointmentCategoryRepositoryInterface $appointmentCategoryRepository, TimetableWeekRepositoryInterface $weekRepository) {
        $this->dateHelper = $dateHelper;
        $this->settings = $settingsManager;
        $this->appointmentRepository = $appointmentRepository;
        $this->appointmentCategoryRepository = $appointmentCategoryRepository;
        $this->freeTimespanRepository = $freeTimespanRepository;
        $this->weekRepository = $weekRepository;
    }

    /**
     * @param WeekOfYear[] $weeks
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @return Timetable
     */
    public function makeTimetable(array $weeks, array $lessons, array $supervision = [ ]): Timetable {
        $timetable = new Timetable();

        $freeDays = $this->getFreeDays();

        foreach($weeks as $week) {
            $timetable->addWeek(
                $this->makeTimetableWeek($week, $lessons, $supervision, $freeDays)
            );
        }

        $this->addEmptyLessons($timetable);
        $this->collapseTimetable($timetable);
        $this->ensureAllLessonsAreDisplayed($timetable);

        return $timetable;
    }

    /**
     * @return DateTime[]
     */
    private function getFreeDays(): array {
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

        foreach($this->freeTimespanRepository->findAll() as $timespan) {
            if($timespan->getStart() === 1 && $timespan->getEnd() === $this->settings->getMaxLessons()) {
                $freeDays[] = $timespan->getDate();
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
                $lessons = $day->getLessonsContainers();

                for($lesson = 1; $lesson <= $maxLessons; $lesson++) {
                    if(array_key_exists($lesson, $lessons) !== true) {
                        $day->addEmptyTimetableLessonsContainer($lesson);
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
                for($lessonNumber = 1; $lessonNumber <= count($day->getLessonsContainers()); $lessonNumber++) {
                    $container = $day->getTimetableLessonsContainer($lessonNumber);

                    if(count($container->getSupervisions()) > 0) {
                        continue; // do not collapse if contains supervisions
                    }

                    if(count($container->getLessons()) === 0) {
                        continue; // no lessons -> continue (important so we can use $durations[0] afterwards)
                    }

                    $durations = [ ];
                    foreach($container->getLessons() as $lesson) {
                        $durations[] = $lesson->getLessonEnd() - $lesson->getLessonStart() + 1;
                    }

                    if(min($durations) !== max($durations) && $durations[0] > 1) { // dirty condition for "not all numbers are same"
                        continue;
                    }

                    $duration = $durations[0];

                    // now check if following lessons have same lessons (they might have additional lessons) or have
                    // supervisions before them (also, then do not collapse)

                    $lessonIds = array_map(function(TimetableLessonEntity $lesson) {
                        return $lesson->getId();
                    }, $container->getLessons());

                    for($nextLessonNumber = $lessonNumber + 1; $nextLessonNumber <= $lessonNumber + $duration - 1; $nextLessonNumber++) {
                        $nextLessonContainer = $day->getTimetableLessonsContainer($nextLessonNumber);

                        if($nextLessonContainer->hasSupervisionBefore()) {
                            continue 2; // continue to outer loop as collapsing is not possible
                        }

                        // Now check if lessons are same
                        $nextLessonIds = array_map(function(TimetableLessonEntity $lesson) {
                            return $lesson->getId();
                        }, $nextLessonContainer->getLessons());

                        if(ArrayUtils::areEqual($lessonIds, $nextLessonIds)) {
                            $container->setRowSpan($container->getRowSpan() + 1);
                            $nextLessonContainer->clear();
                            $nextLessonContainer->setRowSpan(0);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param WeekOfYear $week
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @param DateTime[] $freeDays
     * @return TimetableWeek
     */
    private function makeTimetableWeek(WeekOfYear $week, array $lessons, array $supervision, array $freeDays): TimetableWeek {
        $timetableWeekEntity = $this->weekRepository->findOneByWeekNumber($week->getWeekNumber());

        $timetableWeek = new TimetableWeek($week->getYear(), $week->getWeekNumber(), $timetableWeekEntity?->getDisplayName());

        $lessons = array_filter($lessons, function (TimetableLessonEntity $lesson) use ($week) {
            return $this->dateHelper->isBetween($lesson->getDate(), $week->getFirstDay(), $week->getLastDay());
        });

        $supervision = array_filter($supervision, function(TimetableSupervision $entry) use ($week) {
            return $this->dateHelper->isBetween($entry->getDate(), $week->getFirstDay(), $week->getLastDay());
        });

        for($i = 0; $i < 5; $i++) {
            $date = (clone $week->getFirstDay())->modify(sprintf('+%d days', $i));
            $isCurrent = $this->isCurrentDay($date);
            $isUpcoming = $this->isUpcomingDay($date);
            $isFree = $this->isFree($date, $freeDays);

            $day = $this->makeTimetableDay($date, $isCurrent, $isUpcoming, $isFree, $lessons, $supervision);

            if($isCurrent || $isUpcoming) {
                $timetableWeek->setCurrentOrUpcoming();
            }

            $timetableWeek->days[$i] = $day;
        }

        // Calculate max day lessons
        $max = 0;
        foreach($timetableWeek->days as $day) {
            $lessons = array_keys($day->getLessonsContainers());
            if(count($lessons) > 0) {
                // max() only works with non-empty arrays
                $max = max($max, ...$lessons);
            }
        }

        $timetableWeek->setMaxLesson($max);

        return $timetableWeek;
    }

    /**
     * @param DateTime $date
     * @param bool $isCurrentDay
     * @param bool $isUpcomingDay
     * @param bool $isFree
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervision
     * @return TimetableDay
     */
    private function makeTimetableDay(DateTime $date, bool $isCurrentDay, bool $isUpcomingDay, bool $isFree, array $lessons, array $supervision): TimetableDay {
        $timetableDay = new TimetableDay($date, $isCurrentDay, $isUpcomingDay, $isFree);

        /** @var TimetableLessonEntity[] $lessons */
        $lessons = array_filter($lessons, function(TimetableLessonEntity $lesson) use ($date) {
            return $lesson->getDate() == $date;
        });

        $supervision = array_filter($supervision, function(TimetableSupervision $entry) use($date) {
            return $entry->getDate() == $date;
        });

        foreach($lessons as $lesson) {
            $timetableDay->addTimetableLessonsContainer($lesson);
        }

        foreach($supervision as $entry) {
            $timetableDay->addSupervisionEntry($entry);
        }

        return $timetableDay;
    }

    private function isCurrentDay(DateTime $date): bool {
        $today = $this->dateHelper->getToday();

        return $today == $date;
    }

    private function isUpcomingDay(DateTime $date): bool {
        $today = $this->dateHelper->getToday();

        if($this->isWeekend($today) === false) {
            return false;
        }

        while($this->isWeekend($today)) {
            $today = $today->modify('+1 day');
        }

        return $today == $date;
    }

    /**
     * @param DateTime $dateTime
     * @param DateTime[] $freeDays
     * @return bool
     */
    private function isFree(DateTime $dateTime, array $freeDays): bool {
        foreach($freeDays as $freeDay) {
            if($dateTime == $freeDay) {
                return true;
            }
        }

        return false;
    }

    private function isWeekend(DateTime $dateTime): bool {
        return $dateTime->format('w') == 0 || $dateTime->format('w') == 6;
    }
}