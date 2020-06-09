<?php

namespace App\Rooms\Reservation;

use App\Entity\Room;
use App\Entity\RoomReservation;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;
use App\Repository\RoomReservationRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Settings\DashboardSettings;
use App\Settings\TimetableSettings;
use App\Timetable\TimetablePeriodHelper;
use App\Timetable\TimetableWeekHelper;
use App\Utils\ArrayUtils;
use DateTime;

class RoomAvailabilityHelper {
    private $reservationRepository;
    private $timetableRepository;
    private $timetableSettings;
    private $substitutionRepository;
    private $dashboardSettings;
    private $weekHelper;
    private $periodHelper;

    public function __construct(RoomReservationRepositoryInterface $reservationRepository, TimetableLessonRepositoryInterface $timetableRepository,
                                TimetableSettings $timetableSettings, TimetableWeekHelper $weekHelper, TimetablePeriodHelper $periodHelper,
                                SubstitutionRepositoryInterface $substitutionRepository, DashboardSettings $dashboardSettings) {
        $this->reservationRepository = $reservationRepository;
        $this->timetableRepository = $timetableRepository;
        $this->timetableSettings = $timetableSettings;
        $this->weekHelper = $weekHelper;
        $this->periodHelper = $periodHelper;
        $this->substitutionRepository = $substitutionRepository;
        $this->dashboardSettings = $dashboardSettings;
    }

    /**
     * @param Substitution $substitution
     * @param int $lessonNumber
     * @return bool
     */
    private function isSubstitutionInLesson(Substitution $substitution, int $lessonNumber): bool {
        return $substitution->getLessonStart() <= $lessonNumber && $substitution->getLessonEnd() >= $lessonNumber;
    }

    /**
     * @param Substitution[] $substitutions
     * @param Room $room
     * @param int $lessonNumber
     * @return Substitution|null
     */
    private function getConflictingSubstitution(array $substitutions, Room $room, int $lessonNumber): ?Substitution {
        foreach($substitutions as $substitution) {
            if($this->isSubstitutionInLesson($substitution, $lessonNumber) === false) {
                continue;
            }

            if($substitution->getReplacementRoom() !== null && $substitution->getReplacementRoom() === $room->getExternalId()) {
                return $substitution;
            }
        }

        return null;
    }

    /**
     * @param Substitution[] $substitutions
     * @param Room $room
     * @param int $lessonNumber
     * @return bool
     */
    private function isTimetableLessonCancelled(array $substitutions, Room $room, int $lessonNumber): bool {
        $isCancelled = null;

        foreach($substitutions as $substitution) {
            if($this->isSubstitutionInLesson($substitution, $lessonNumber) === false) {
                continue;
            }

            // Case 1: there is a substitution away from the room (potentially making the room available)
            if($substitution->getRoom() === $room->getExternalId()
                && $substitution->getReplacementRoom() !== null
                && $substitution->getReplacementRoom() !== $room->getExternalId()
                && $isCancelled === null ) { // Only cancel substitution if not previously being set to false
                $isCancelled = true;
            }

            // Case 2: there is a cancellation
            if($substitution->getRoom() === $room->getExternalId()
                && in_array($substitution->getType(), $this->dashboardSettings->getFreeLessonSubstitutionTypes())
                && $isCancelled === null) { // Only cancel substitution if not previously being set to false
                $isCancelled = true;
            }
        }

        return $isCancelled ?? false;
    }

    public function getAvailability(Room $room, DateTime $date, int $lessonNumber): ?RoomAvailability {
        $week = $this->weekHelper->getTimetableWeek($date);
        $period = $this->periodHelper->getPeriod($date);

        if($period === null || $week === null) {
            return null;
        }

        $lesson = $this->timetableRepository->findOneByPeriodAndRoomAndWeekAndDayAndLesson($period, $week, $room, $date->format('w'), $lessonNumber);
        $reservation = $this->reservationRepository->findOneByDateAndRoomAndLesson($date, $room, $lessonNumber);

        $substitutions = $this->substitutionRepository->findAllForRooms([$room], $date);
        $conflictingSubstitution = $this->getConflictingSubstitution($substitutions, $room, $lessonNumber);

        $availability = new RoomAvailability($reservation, $lesson, $conflictingSubstitution);

        if($this->isTimetableLessonCancelled($substitutions, $room, $lessonNumber)) {
            $availability->setTimetableLessonCancelled();
        }

        return $availability;
    }

    /**
     * @param DateTime $date
     * @param Room[] $rooms
     * @return RoomAvailabilityOverview
     */
    public function getAvailabilities(DateTime $date, array $rooms): RoomAvailabilityOverview {
        $week = $this->weekHelper->getTimetableWeek($date);
        $period = $this->periodHelper->getPeriod($date);

        $lessons = $this->timetableRepository->findAllByPeriodAndWeek($period, $week);
        $reservations = $this->reservationRepository->findAllByDate($date);
        $substitutions = $this->substitutionRepository->findAllForRooms($rooms, $date);

        $overview = new RoomAvailabilityOverview($this->timetableSettings->getMaxLessons());

        foreach($rooms as $room) {
            $roomLessons = array_filter($lessons, function(TimetableLesson $lesson) use($room, $date) {
                return $lesson->getRoom() !== null && $lesson->getRoom()->getId() === $room->getId()
                    && $lesson->getDay() === (int)$date->format('w');
            });
            $roomReservations = array_filter($reservations, function(RoomReservation $reservation) use ($room) {
                return $reservation->getRoom()->getId() === $room->getId();
            });
            $roomSubstitutions = array_filter($substitutions, function(Substitution $substitution) use ($room) {
                return ($substitution->getRoom() !== null && $substitution->getRoom() === $room->getExternalId())
                    || ($substitution->getReplacementRoom() !== null && $substitution->getReplacementRoom() === $room->getExternalId());
            });

            for($lessonNumber = 1; $lessonNumber <= $this->timetableSettings->getMaxLessons(); $lessonNumber++) {
                $lesson = ArrayUtils::first($roomLessons, function(TimetableLesson $lesson) use ($lessonNumber) {
                    return $lesson->getLesson() === $lessonNumber
                        || ($lesson->isDoubleLesson() && $lesson->getLesson() === $lessonNumber - 1);
                });
                $reservation = ArrayUtils::first($roomReservations, function (RoomReservation $reservation) use ($lessonNumber) {
                    return $reservation->getLessonStart() <= $lessonNumber
                        && $reservation->getLessonEnd() >= $lessonNumber;
                });
                $substitution = $this->getConflictingSubstitution($roomSubstitutions, $room, $lessonNumber);

                $availability = new RoomAvailability($reservation, $lesson, $substitution);

                if($this->isTimetableLessonCancelled($roomSubstitutions, $room, $lessonNumber)) {
                    $availability->setTimetableLessonCancelled();
                }

                $overview->addAvailability($room, $lessonNumber, $availability);
            }
        }

        return $overview;
    }
}