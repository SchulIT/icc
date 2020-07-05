<?php

namespace App\Timetable;

use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Settings\TimetableSettings;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;

/**
 * This helper creates a series of events based on the timetable.
 */
class TimetableCalenderExportHelper {

    private $dateHelper;
    private $timetableSettings;
    private $timetableWeekRepository;
    private $appointmentsRepository;

    public function __construct(DateHelper $dateHelper, TimetableSettings $timetableSettings, TimetableWeekRepositoryInterface $weekRepository, AppointmentRepositoryInterface $appointmentRepository) {
        $this->dateHelper = $dateHelper;
        $this->timetableSettings = $timetableSettings;
        $this->timetableWeekRepository = $weekRepository;
        $this->appointmentsRepository = $appointmentRepository;
    }

    /**
     * @param TimetablePeriod $period
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervisions
     * @return TimetableCalendarDayView[]
     */
    public function getLessonsTimeline(TimetablePeriod $period, array $lessons, array $supervisions): array {
        $views = [ ];
        $numberOfWeeks = count($this->timetableWeekRepository->findAll());

        $currentDay = clone $period->getStart();

        while($currentDay <= $period->getEnd()) {
            $views[] = $this->createCalendarDayView($currentDay, $numberOfWeeks, $lessons, $supervisions);

            $currentDay->modify('+1 day');
        }

        return $views;
    }

    /**
     * @param DateTime $day
     * @param int $numberOfWeeks
     * @param TimetableLessonEntity[] $lessons
     * @param TimetableSupervision[] $supervisions
     * @return TimetableCalendarDayView
     */
    private function createCalendarDayView(DateTime $day, int $numberOfWeeks, array $lessons, array $supervisions) {
        // TODO: appointments

        $weekNumber = (int)$day->format('W');
        $dayNumber = (int)$day->format('N');

        $dayLessons = array_filter($lessons, function(TimetableLessonEntity $lesson) use ($numberOfWeeks, $weekNumber, $dayNumber) {
            return $lesson->getDay() === $dayNumber && $weekNumber % $numberOfWeeks === $lesson->getWeek()->getWeekMod();
        });
        $daySupervisions = array_filter($supervisions, function(TimetableSupervision $supervision) use ($numberOfWeeks, $weekNumber, $dayNumber) {
            return $supervision->getDay() === $dayNumber && $weekNumber % $numberOfWeeks === $supervision->getWeek()->getWeekMod();
        });

        return new TimetableCalendarDayView($day, $dayLessons, $daySupervisions);
    }
}