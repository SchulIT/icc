<?php

namespace App\Export;

use App\Entity\Grade;
use App\Entity\TimetableLesson;
use App\Entity\TimetableSupervision;
use App\Entity\User;
use App\Ics\IcsHelper;
use App\Import\ReplaceImportStrategyInterface;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetableTimeHelper;
use App\Tools\CountdownCalculator;
use App\Utils\ArrayUtils;
use DateTime;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimetableIcsExporter {

    public function __construct(private readonly Sorter $sorter, private readonly TranslatorInterface $translator, private readonly TimetableSettings $timetableSettings, private readonly TimetableLessonRepositoryInterface $lessonRepository,
                                private readonly TimetableSupervisionRepositoryInterface $supervisionRepository, private readonly IcsHelper $icsHelper, private readonly TimetableTimeHelper $timetableTimeHelper, private readonly CountdownCalculator $countdownCalculator)
    {
    }

    public function getIcsResponse(User $user): Response {
        $freeDays = $this->getFreeDays();
        $events = $this->makeIcsItems($user, $freeDays);

        return $this->icsHelper->getIcsResponse(
            $this->translator->trans('plans.timetable.export.title'),
            $this->translator->trans('plans.timetable.export.description', [ '%name%' => $user->getUsername() ]),
            $events,
            $this->translator->trans('plans.timetable.export.download.filename')
        );
    }


    /**
     * @param DateTime[] $freeDays
     * @return CalendarEvent[]
     */
    private function makeIcsItems(User $user, array $freeDays): array {
        $events = [ ];

        $start = $this->timetableSettings->getStartDate($user->getUserType());
        $end = $this->timetableSettings->getEndDate($user->getUserType());

        if($start === null || $end === null || $start > $end) {
            return [ ];
        }

        $lessons = [ ];
        $supervisions = [ ];

        if($user->getStudents()->count() > 0) {
            foreach($user->getStudents() as $student) {
                $lessons = array_merge($lessons, $this->lessonRepository->findAllByStudent($start, $end, $student));
            }
        } else if($user->getTeacher() !== null) {
            $lessons = $this->lessonRepository->findAllByTeacher($start, $end, $user->getTeacher());
            $supervisions = $this->supervisionRepository->findAllByTeacher($start, $end, $user->getTeacher());
        }

        $filter = fn(TimetableLesson|TimetableSupervision $item) => !in_array($item->getDate(), $freeDays);

        $lessons = array_filter($lessons, $filter);
        $supervisions = array_filter($supervisions, $filter);

        foreach($lessons as $lesson) {
            $events[] = $this->makeIcsItemForLesson($lesson);
        }

        foreach($supervisions as $supervision) {
            $events[] = $this->makeIcsItemForSupervision($supervision);
        }

        return array_filter($events, fn(?CalendarEvent $event) => $event !== null);
    }

    private function getSubject(TimetableLesson $lesson): string {
        // initialize
        $gradesWithCourseNames = $this->timetableSettings->getGradeIdsWithCourseNames();

        if($lesson->getTuition()) {
            $subject = $lesson->getTuition()->getSubject()->getAbbreviation();
            $grades = $this->getGradesAsString($lesson->getTuition()->getStudyGroup()->getGrades()->toArray());

            foreach ($lesson->getTuition()->getStudyGroup()->getGrades() as $grade) {
                if (in_array($grade->getId(), $gradesWithCourseNames)) {
                    $subject = $lesson->getTuition()->getStudyGroup()->getName();
                }
            }

            return sprintf('%s - %s', $subject, $grades);
        }

        return $lesson->getSubject() ?? ($lesson->getSubjectName() ?? 'N/A');
    }

    private function getGradesAsString(array $grades): string {
        $this->sorter->sort($grades, GradeNameStrategy::class);

        return implode(', ', array_map(fn(Grade $grade) => $grade->getName(), $grades));
    }

    private function makeIcsItemForLesson(TimetableLesson $lesson): CalendarEvent {
        $event = new CalendarEvent();
        $event->setUid(sprintf('timetable-%d-%d-%d', $lesson->getDate()->format('W'), $lesson->getDate()->format('N'), $lesson->getLessonStart()));
        $event->setAllDay(false);
        $event->setSummary($this->getSubject($lesson));

        $event->setStart($this->timetableTimeHelper->getLessonStartDateTime($lesson->getDate(), $lesson->getLessonStart()));
        $event->setEnd($this->timetableTimeHelper->getLessonEndDateTime($lesson->getDate(), $lesson->getLessonEnd()));

        if($lesson->getTuition() !== null) {
            $teacher = $lesson->getTuition()->getTeachers()->first();
            if ($teacher !== null) {
                $organizer = new Organizer(new Formatter());
                $organizer->setName(sprintf('%s %s', $teacher->getFirstname(), $teacher->getLastname()));
                $organizer->setValue($teacher->getEmail());

                $event->setOrganizer($organizer);
            }

            if($lesson->getRoom() !== null) {
                $location = new Location();
                $location->setName($lesson->getRoom()->getName());
                $event->setLocations([$location]);
            }
        } else {
            foreach($lesson->getTeachers() as $teacher) {
                $organizer = new Organizer(new Formatter());
                $organizer->setName(sprintf('%s %s', $teacher->getFirstname(), $teacher->getLastname()));
                $organizer->setValue($teacher->getEmail());

                $event->setOrganizer($organizer);
            }
        }

        return $event;
    }

    private function makeIcsItemForSupervision(TimetableSupervision $supervision): CalendarEvent {
        $event = new CalendarEvent();
        $event->setUid(sprintf('supervision-%d-%d-%d-%d', $supervision->getDate()->format('W'), $supervision->getDate()->format('N'), $supervision->getLesson(), $supervision->isBefore()));
        $event->setAllDay(false);
        $event->setSummary($this->timetableSettings->getSupervisionLabel());

        if($supervision->getLesson() > $this->timetableSettings->getMaxLessons()) {
            $event->setStart($this->timetableTimeHelper->getLessonEndDateTime($supervision->getDate(), $this->timetableSettings->getMaxLessons()));
            $event->setEnd($this->timetableTimeHelper->getDateTime($supervision->getDate(), $this->timetableSettings->getEndOfSupervisionAfterLastLesson()));
        } else {
            $event->setStart($this->timetableTimeHelper->getLessonStartDateTime($supervision->getDate(), $supervision->getLesson(), $supervision->isBefore()));
            $event->setEnd($this->timetableTimeHelper->getLessonEndDateTime($supervision->getDate(), $supervision->getLesson(), $supervision->isBefore()));
        }

        $teacher = $supervision->getTeacher();
        $organizer = new Organizer(new Formatter());
        $organizer->setName(sprintf('%s %s', $teacher->getFirstname(), $teacher->getLastname()));
        $organizer->setValue($teacher->getEmail());

        $event->setOrganizer($organizer);

        if(!empty($supervision->getLocation())) {
            $location = new Location();
            $location->setName($supervision->getLocation());
            $event->setLocations([$location]);
        }

        return $event;
    }

    /**
     * @return DateTime[]
     */
    private function getFreeDays(): array {
        return $this->countdownCalculator->getFreeDays();
    }
}