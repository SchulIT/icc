<?php

namespace App\Rooms\Reservation;

use App\Entity\Exam;
use App\Entity\Resource;
use App\Entity\Room;
use App\Entity\ResourceReservation;
use App\Entity\Substitution;
use App\Entity\TimetableLesson;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ResourceReservationRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Settings\DashboardSettings;
use App\Settings\TimetableSettings;
use App\Timetable\TimetablePeriodHelper;
use App\Timetable\TimetableWeekHelper;
use App\Utils\ArrayUtils;
use DateTime;

class ResourceAvailabilityHelper {
    private $reservationRepository;
    private $timetableRepository;
    private $timetableSettings;
    private $substitutionRepository;
    private $examRepository;
    private $dashboardSettings;
    private $weekHelper;
    private $periodHelper;

    public function __construct(ResourceReservationRepositoryInterface $reservationRepository, TimetableLessonRepositoryInterface $timetableRepository,
                                TimetableSettings $timetableSettings, TimetableWeekHelper $weekHelper, TimetablePeriodHelper $periodHelper,
                                SubstitutionRepositoryInterface $substitutionRepository, ExamRepositoryInterface $examRepository, DashboardSettings $dashboardSettings) {
        $this->reservationRepository = $reservationRepository;
        $this->timetableRepository = $timetableRepository;
        $this->timetableSettings = $timetableSettings;
        $this->weekHelper = $weekHelper;
        $this->periodHelper = $periodHelper;
        $this->substitutionRepository = $substitutionRepository;
        $this->examRepository = $examRepository;
        $this->dashboardSettings = $dashboardSettings;
    }

    /**
     * @param Substitution $substitution
     * @param int $lessonNumber
     * @return bool
     */
    private function isSubstitutionInLesson(Substitution $substitution, int $lessonNumber): bool {
        return $substitution->startsBefore() === false && $substitution->getLessonStart() <= $lessonNumber && $substitution->getLessonEnd() >= $lessonNumber;
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

            if($substitution->getReplacementRoom() !== null && $substitution->getReplacementRoom() === $room) {
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

    public function getAvailability(Resource $resource, DateTime $date, int $lessonNumber): ?ResourceAvailability {
        $week = $this->weekHelper->getTimetableWeek($date);
        $period = $this->periodHelper->getPeriod($date);

        if($period === null || $week === null) {
            return null;
        }

        $lesson = null;
        $conflictingSubstitution = null;
        $conflictingExams = [ ];

        if($resource instanceof Room) {
            $lesson = $this->timetableRepository->findOneByPeriodAndRoomAndWeekAndDayAndLesson($period, $week, $resource, (int)$date->format('w'), $lessonNumber);
            $substitutions = $this->substitutionRepository->findAllForRooms([$resource], $date);
            $conflictingSubstitution = $this->getConflictingSubstitution($substitutions, $resource, $lessonNumber);

            $conflictingExams = $this->examRepository->findAllByRoomAndDateAndLesson($resource, $date, $lessonNumber);
        }

        $reservation = $this->reservationRepository->findOneByDateAndResourceAndLesson($date, $resource, $lessonNumber);
        $availability = new ResourceAvailability($reservation, $lesson, $conflictingSubstitution, $conflictingExams);

        if($resource instanceof Room && $this->isTimetableLessonCancelled($substitutions, $resource, $lessonNumber)) {
            $availability->setTimetableLessonCancelled();
        }

        return $availability;
    }

    /**
     * @param DateTime $date
     * @param Resource[] $resources
     * @return ResourceAvailabilityOverview|null
     */
    public function getAvailabilities(DateTime $date, array $resources): ?ResourceAvailabilityOverview {
        $week = $this->weekHelper->getTimetableWeek($date);
        $period = $this->periodHelper->getPeriod($date);

        if($period === null || $week === null) {
            return null;
        }

        /** @var Room[] $rooms */
        $rooms = array_filter($resources, function(Resource $resource) {
            return $resource instanceof Room;
        });

        $lessons = $this->timetableRepository->findAllByPeriodAndWeek($period, $week);
        $reservations = $this->reservationRepository->findAllByDate($date);
        $substitutions = $this->substitutionRepository->findAllForRooms($rooms, $date);
        $exams = $this->examRepository->findAllByDate($date);

        $overview = new ResourceAvailabilityOverview($this->timetableSettings->getMaxLessons());

        foreach($resources as $resource) {
            $roomLessons = array_filter($lessons, function(TimetableLesson $lesson) use($resource, $date) {
                return $lesson instanceof TimetableLesson
                    && $lesson->getRoom() !== null && $lesson->getRoom()->getId() === $resource->getId()
                    && $lesson->getDay() === (int)$date->format('w');
            });
            $roomReservations = array_filter($reservations, function(ResourceReservation $reservation) use ($resource) {
                return $reservation->getResource()->getId() === $resource->getId();
            });
            $roomSubstitutions = array_filter($substitutions, function(Substitution $substitution) use ($resource) {
                return ($substitution->getRoom() !== null && $substitution->getRoom() === $resource)
                    || ($substitution->getReplacementRoom() !== null && $substitution->getReplacementRoom() === $resource);
            });

            for($lessonNumber = 1; $lessonNumber <= $this->timetableSettings->getMaxLessons(); $lessonNumber++) {
                $lesson = ArrayUtils::first($roomLessons, function(TimetableLesson $lesson) use ($lessonNumber) {
                    return $lesson->getLesson() === $lessonNumber
                        || ($lesson->isDoubleLesson() && $lesson->getLesson() === $lessonNumber - 1);
                });
                $reservation = ArrayUtils::first($roomReservations, function (ResourceReservation $reservation) use ($lessonNumber) {
                    return $reservation->getLessonStart() <= $lessonNumber
                        && $reservation->getLessonEnd() >= $lessonNumber;
                });

                $substitution = null;
                $collidingExams = [ ];

                if($resource instanceof Room) {
                    $substitution = $this->getConflictingSubstitution($roomSubstitutions, $resource, $lessonNumber);

                    $collidingExams = array_filter($exams, function (Exam $exam) use ($lessonNumber, $resource) {
                        return $exam->getRoom() != null && $exam->getRoom()->getId() === $resource->getId()
                            && $exam->getLessonStart() <= $lessonNumber && $lessonNumber <= $exam->getLessonEnd();
                    });
                }

                $availability = new ResourceAvailability($reservation, $lesson, $substitution, $collidingExams);

                if($resource instanceof Room && $this->isTimetableLessonCancelled($roomSubstitutions, $resource, $lessonNumber)) {
                    $availability->setTimetableLessonCancelled();
                }

                $overview->addAvailability($resource, $lessonNumber, $availability);
            }
        }

        return $overview;
    }
}