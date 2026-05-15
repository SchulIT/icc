<?php

namespace App\Room\Reservation;

use App\Substitution\Entity\Absence;
use App\Exam\Entity\Exam;
use App\Common\Entity\ResourceEntity;
use App\Common\Entity\Room;
use App\Room\Entity\ResourceReservation;
use App\Substitution\Entity\Substitution;
use App\Timetable\Entity\TimetableLesson;
use App\Substitution\Repository\AbsenceRepositoryInterface;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Common\Repository\ResourceReservationRepositoryInterface;
use App\Substitution\Repository\SubstitutionRepositoryInterface;
use App\Timetable\Repository\TimetableLessonRepositoryInterface;
use App\Dashboard\Settings\DashboardSettings;
use App\Timetable\Settings\TimetableSettings;
use App\Framework\Utils\ArrayUtils;
use DateTime;

class ResourceAvailabilityHelper {
    public function __construct(private readonly ResourceReservationRepositoryInterface $reservationRepository, private readonly TimetableLessonRepositoryInterface $timetableRepository,
                                private readonly TimetableSettings $timetableSettings, private readonly SubstitutionRepositoryInterface $substitutionRepository,
                                private readonly ExamRepositoryInterface $examRepository, private readonly DashboardSettings $dashboardSettings,
                                private readonly AbsenceRepositoryInterface $absenceRepository)
    {
    }

    private function isSubstitutionInLesson(Substitution $substitution, int $lessonNumber): bool {
        return $substitution->startsBefore() === false && $substitution->getLessonStart() <= $lessonNumber && $substitution->getLessonEnd() >= $lessonNumber;
    }

    /**
     * @param Substitution[] $substitutions
     */
    private function getConflictingSubstitution(array $substitutions, Room $room, int $lessonNumber): ?Substitution {
        foreach($substitutions as $substitution) {
            if($this->isSubstitutionInLesson($substitution, $lessonNumber) === false) {
                continue;
            }

            if($substitution->getReplacementRooms()->count() > 0 && $substitution->getReplacementRooms()->contains($room)) {
                return $substitution;
            }
        }

        return null;
    }

    /**
     * @param Substitution[] $substitutions
     */
    private function isTimetableLessonCancelled(array $substitutions, Room $room, int $lessonNumber): bool {
        $isCancelled = null;

        foreach($substitutions as $substitution) {
            if($this->isSubstitutionInLesson($substitution, $lessonNumber) === false) {
                continue;
            }

            // Case 1: there is a substitution away from the room (potentially making the room available)
            $rooms = $substitution->getRooms()->map(fn(Room $room) => $room->getId());
            $replacementRooms = $substitution->getReplacementRooms()->map(fn(Room $room) => $room->getId());

            if($rooms->contains($room->getId())
                && $replacementRooms->contains($room->getId()) === false
                && $isCancelled === null ) { // Only cancel substitution if not previously being set to false
                $isCancelled = true;
            }

            // Case 2: there is a cancellation
            if($rooms->contains($room->getId())
                && in_array($substitution->getType(), $this->dashboardSettings->getFreeLessonSubstitutionTypes())
                && $isCancelled === null) { // Only cancel substitution if not previously being set to false
                $isCancelled = true;
            }
        }

        return $isCancelled ?? false;
    }

    public function getAvailability(ResourceEntity $resource, DateTime $date, int $lessonNumber): ?ResourceAvailability {
        $lesson = null;
        $conflictingSubstitution = null;
        $conflictingExams = [ ];
        $absences = [ ];

        if($resource instanceof Room) {
            $lesson = $this->timetableRepository->findOneByDateAndRoomAndLesson($date, $resource, $lessonNumber);
            $substitutions = $this->substitutionRepository->findAllForRooms([$resource], $date);
            $absences = array_filter(
                $this->absenceRepository->findAllByRoomAndDate($resource, $date),
                fn(Absence $absence) => $absence->getLessonStart() === null || $absence->getLessonEnd() === null || ($absence->getLessonStart() <= $lessonNumber && $lessonNumber <= $absence->getLessonEnd())
            );

            $conflictingSubstitution = $this->getConflictingSubstitution($substitutions, $resource, $lessonNumber);

            $conflictingExams = $this->examRepository->findAllByRoomAndDateAndLesson($resource, $date, $lessonNumber);
        }

        $reservation = $this->reservationRepository->findOneByDateAndResourceAndLesson($date, $resource, $lessonNumber);
        $availability = new ResourceAvailability($reservation, $lesson, $conflictingSubstitution, $conflictingExams, $absences);

        if($resource instanceof Room && $this->isTimetableLessonCancelled($substitutions, $resource, $lessonNumber)) {
            $availability->setTimetableLessonCancelled();
        }

        return $availability;
    }

    /**
     * @param ResourceEntity[] $resources
     */
    public function getAvailabilities(DateTime $date, array $resources): ?ResourceAvailabilityOverview {
        /** @var Room[] $rooms */
        $rooms = array_filter($resources, fn(ResourceEntity $resource) => $resource instanceof Room);

        $lessons = $this->timetableRepository->findAllByRange($date, $date);
        $reservations = $this->reservationRepository->findAllByDate($date);
        $substitutions = $this->substitutionRepository->findAllForRooms($rooms, $date);
        $exams = $this->examRepository->findAllByDate($date);

        $overview = new ResourceAvailabilityOverview($this->timetableSettings->getMaxLessons());

        foreach($resources as $resource) {
            $absences = [ ];

            if($resource instanceof Room) {
                $absences = $this->absenceRepository->findAllByRoomAndDate($resource, $date);
            }

            $roomLessons = array_filter($lessons, fn(TimetableLesson $lesson) => $lesson->getRoom() !== null && $lesson->getRoom()->getId() === $resource->getId());
            $roomReservations = array_filter($reservations, fn(ResourceReservation $reservation) => $reservation->getResource()->getId() === $resource->getId());
            $roomSubstitutions = array_filter($substitutions, fn(Substitution $substitution) => $substitution->getRooms()->contains($resource)
                || ($substitution->getReplacementRooms()->contains($resource)));

            for($lessonNumber = 1; $lessonNumber <= $this->timetableSettings->getMaxLessons(); $lessonNumber++) {
                $lesson = ArrayUtils::first($roomLessons, fn(TimetableLesson $lesson) => $lesson->getLessonStart() <= $lessonNumber && $lessonNumber <= $lesson->getLessonEnd());
                $reservation = ArrayUtils::first($roomReservations, fn(ResourceReservation $reservation) => $reservation->getLessonStart() <= $lessonNumber
                    && $reservation->getLessonEnd() >= $lessonNumber);

                $substitution = null;
                $collidingExams = [ ];
                $collidingAbsences = [ ];

                if($resource instanceof Room) {
                    $substitution = $this->getConflictingSubstitution($roomSubstitutions, $resource, $lessonNumber);

                    $collidingExams = array_filter($exams, fn(Exam $exam) => $exam->getRoom() != null && $exam->getRoom()->getId() === $resource->getId()
                        && $exam->getLessonStart() <= $lessonNumber && $lessonNumber <= $exam->getLessonEnd());

                    $collidingAbsences = array_filter(
                        $absences,
                        fn(Absence $absence) => $absence->getLessonStart() === null || $absence->getLessonEnd() === null || ($absence->getLessonStart() <= $lessonNumber && $lessonNumber <= $absence->getLessonEnd())
                    );
                }

                $availability = new ResourceAvailability($reservation, $lesson, $substitution, $collidingExams, $collidingAbsences);

                if($resource instanceof Room && $this->isTimetableLessonCancelled($roomSubstitutions, $resource, $lessonNumber)) {
                    $availability->setTimetableLessonCancelled();
                }

                $overview->addAvailability($resource, $lessonNumber, $availability);
            }
        }

        return $overview;
    }
}