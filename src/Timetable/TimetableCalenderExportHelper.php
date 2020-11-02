<?php

namespace App\Timetable;

use App\Entity\Appointment;
use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Security\Voter\AppointmentVoter;
use App\Settings\TimetableSettings;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * This helper creates a series of events based on the timetable.
 */
class TimetableCalenderExportHelper {

    private $dateHelper;
    private $timetableSettings;
    private $timetableWeekRepository;
    private $appointmentsRepository;
    private $appointmentCategoryRepository;
    private $authorizationChecker;

    public function __construct(DateHelper $dateHelper, TimetableSettings $timetableSettings,
                                TimetableWeekRepositoryInterface $weekRepository, AppointmentRepositoryInterface $appointmentRepository,
                                AppointmentCategoryRepositoryInterface $appointmentCategoryRepository, AuthorizationCheckerInterface $authorizationChecker) {
        $this->dateHelper = $dateHelper;
        $this->timetableSettings = $timetableSettings;
        $this->timetableWeekRepository = $weekRepository;
        $this->appointmentsRepository = $appointmentRepository;
        $this->appointmentCategoryRepository = $appointmentCategoryRepository;
        $this->authorizationChecker = $authorizationChecker;
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
        $freeDays = $this->computeFreeDays($period->getStart(), $period->getEnd());

        while($currentDay <= $period->getEnd()) {
            $currentDayString = $currentDay->format('Y-m-d');
            if(in_array($currentDayString, $freeDays)) {
                $currentDay = (clone $currentDay)->modify('+1 day');
                continue;
            }

            $views[] = $this->createCalendarDayView($currentDay, $numberOfWeeks, $lessons, $supervisions);

            $currentDay = (clone $currentDay)->modify('+1 day');
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

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return string[]
     */
    private function computeFreeDays(DateTime $start, DateTime $end) {
        $categories = [ ];

        foreach($this->timetableSettings->getCategoryIds() as $id) {
            $category = $this->appointmentCategoryRepository->findOneById($id);
            if($category !== null) {
                $categories[] = $category;
            }
        }

        $appointments = $this->appointmentsRepository->findAllStartEnd($start, $end, $categories);
        $appointments = array_filter($appointments, function(Appointment $appointment) {
            return $this->authorizationChecker->isGranted(AppointmentVoter::View, $appointment);
        });

        $freeDays = [ ];

        foreach($appointments as $appointment) {
            $freeDays = array_merge(
                $freeDays,
                array_map(function(DateTime $dateTime) {
                    return $dateTime->format('Y-m-d');
                }, $this->expandDatespan($appointment->getStart(), $appointment->getEnd()))
            );
        }

        return $freeDays;
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return DateTime[]
     */
    private function expandDatespan(DateTime $start, DateTime $end) {
        $dates = [ ];

        $start = clone $start;

        while($start < $end) {
            $dates[] = $start;
            $start = (clone $start)->modify('+1 day');
        }

        return $dates;
    }
}