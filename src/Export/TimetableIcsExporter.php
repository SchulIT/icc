<?php

namespace App\Export;

use App\Entity\Grade;
use App\Entity\TimetableLesson;
use App\Entity\TimetableSupervision;
use App\Entity\User;
use App\Ics\IcsHelper;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetableTimeHelper;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimetableIcsExporter {

    private Sorter $sorter;
    private TranslatorInterface $translator;
    private TimetableSettings $timetableSettings;
    private TimetableTimeHelper $timetableTimeHelper;
    private IcsHelper $icsHelper;

    private TimetableLessonRepositoryInterface $lessonRepository;
    private TimetableSupervisionRepositoryInterface $supervisionRepository;

    public function __construct(Sorter $sorter, TranslatorInterface $translator, TimetableSettings $timetableSettings,
                                TimetableLessonRepositoryInterface $lessonRepository,  TimetableSupervisionRepositoryInterface $supervisionRepository,
                                IcsHelper $icsHelper, TimetableTimeHelper $timetableTimeHelper) {
        $this->sorter = $sorter;
        $this->translator = $translator;
        $this->timetableSettings = $timetableSettings;
        $this->timetableTimeHelper = $timetableTimeHelper;
        $this->icsHelper = $icsHelper;

        $this->lessonRepository = $lessonRepository;
        $this->supervisionRepository = $supervisionRepository;
    }

    public function getIcsResponse(User $user): Response {
        $events = $this->makeIcsItems($user);

        return $this->icsHelper->getIcsResponse(
            $this->translator->trans('plans.timetable.export.title'),
            $this->translator->trans('plans.timetable.export.description', [ '%name%' => $user->getUsername() ]),
            $events,
            $this->translator->trans('plans.timetable.export.download.filename')
        );
    }


    /**
     * @param User $user
     * @return CalendarEvent[]
     */
    private function makeIcsItems(User $user): array {
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

        foreach($lessons as $lesson) {
            $events[] = $this->makeIcsItemForLesson($lesson);
        }

        foreach($supervisions as $supervision) {
            $events[] = $this->makeIcsItemForSupervision($supervision);
        }

        return array_filter($events, function(?CalendarEvent $event) {
            return $event !== null;
        });
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

        return implode(', ', array_map(function(Grade $grade) {
            return $grade->getName();
        }, $grades));
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

        $event->setStart($this->timetableTimeHelper->getLessonStartDateTime($supervision->getDate(), $supervision->getLesson(), $supervision->isBefore()));
        $event->setEnd($this->timetableTimeHelper->getLessonEndDateTime($supervision->getDate(), $supervision->getLesson(), $supervision->isBefore()));

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
}