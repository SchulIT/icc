<?php

namespace App\Export;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TimetableSupervision;
use App\Entity\User;
use App\Ics\IcsHelper;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Security\Voter\TimetablePeriodVoter;
use App\Settings\TimetableSettings;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetableCalenderExportHelper;
use App\Timetable\TimetableTimeHelper;
use DateInterval;
use DateTime;
use Exception;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimetableIcsExporter {

    private $sorter;
    private $translator;
    private $timetableSettings;
    private $timetableExportHelper;
    private $timetableTimeHelper;
    private $icsHelper;
    private $authorizationChecker;

    private $lessonRepository;
    private $periodRepository;
    private $supervisionRepository;

    public function __construct(Sorter $sorter, TranslatorInterface $translator, TimetableSettings $timetableSettings, TimetableLessonRepositoryInterface $lessonRepository, TimetablePeriodRepositoryInterface $periodRepository,
                                TimetableSupervisionRepositoryInterface $supervisionRepository, TimetableCalenderExportHelper $timetableExportHelper,
                                IcsHelper $icsHelper, TimetableTimeHelper $timetableTimeHelper, AuthorizationCheckerInterface $authorizationChecker) {
        $this->sorter = $sorter;
        $this->translator = $translator;
        $this->timetableSettings = $timetableSettings;
        $this->timetableExportHelper = $timetableExportHelper;
        $this->timetableTimeHelper = $timetableTimeHelper;
        $this->icsHelper = $icsHelper;
        $this->authorizationChecker = $authorizationChecker;

        $this->lessonRepository = $lessonRepository;
        $this->periodRepository = $periodRepository;
        $this->supervisionRepository = $supervisionRepository;
    }

    public function getIcsResponse(User $user): Response {
        $events = $this->makeIcsItems($user);

        return $this->icsHelper->getIcsResponse(
            $this->translator->trans('plans.timetable.export.title'),
            $this->translator->trans('plans.timetable.export.description', [ '%user%' => $user->getUsername() ]),
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

        foreach($this->periodRepository->findAll() as $period) {
            if($this->authorizationChecker->isGranted(TimetablePeriodVoter::View, $period)) {
                $lessons = [ ];
                $supervisions = [ ];

                if($user->getStudents()->count() > 0) {
                    foreach($user->getStudents() as $student) {
                        $lessons = array_merge($lessons, $this->lessonRepository->findAllByPeriodAndStudent($period, $student));
                    }
                } else if($user->getTeacher() !== null) {
                    $lessons = $this->lessonRepository->findAllByPeriodAndTeacher($period, $user->getTeacher());
                    $supervisions = $this->supervisionRepository->findAllByPeriodAndTeacher($period, $user->getTeacher());
                }

                foreach($this->timetableExportHelper->getLessonsTimeline($period, $lessons, $supervisions) as $view) {
                    foreach($view->getLessons() as $lesson) {
                        $events[] = $this->makeIcsItemForLesson($view->getDay(), $lesson);
                    }

                    foreach($view->getSupervisions() as $supervision) {
                        $events[] = $this->makeIcsItemForSupervision($view->getDay(), $supervision);
                    }
                }
            }
        }

        return array_filter($events, function(?CalendarEvent $event) {
            return $event !== null;
        });
    }

    private function getSubject(TimetableLesson $lesson): string {
        // initialize
        $gradesWithCourseNames = $this->timetableSettings->getGradeIdsWithCourseNames();

        $subject = $lesson->getTuition()->getSubject()->getAbbreviation();
        $grades = $this->getGradesAsString($lesson->getTuition()->getStudyGroup()->getGrades()->toArray());

        foreach($lesson->getTuition()->getStudyGroup()->getGrades() as $grade) {
            if(in_array($grade->getId(), $gradesWithCourseNames)) {
                $subject = $lesson->getTuition()->getStudyGroup()->getName();
            }
        }

        return sprintf('%s - %s', $subject, $grades);
    }

    private function getGradesAsString(array $grades): string {
        $this->sorter->sort($grades, GradeNameStrategy::class);

        return implode(', ', array_map(function(Grade $grade) {
            return $grade->getName();
        }, $grades));
    }

    private function makeIcsItemForLesson(DateTime $day, TimetableLesson $lesson): ?CalendarEvent {
        $event = new CalendarEvent();
        $event->setUid(sprintf('timetable-%d-%d-%d', $day->format('W'), $day->format('N'), $lesson->getLesson()));
        $event->setAllDay(false);
        $event->setSummary($this->getSubject($lesson));

        $event->setStart($this->timetableTimeHelper->getLessonStartDateTime($day, $lesson->getLesson()));
        $event->setEnd($this->timetableTimeHelper->getLessonEndDateTime($day, $lesson->getLesson() + ($lesson->isDoubleLesson() ? 1 : 0)));

        $teacher = $lesson->getTuition()->getTeacher();
        if($teacher !== null) {
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

        return $event;
    }

    private function makeIcsItemForSupervision(DateTime $day, TimetableSupervision $supervision): ?CalendarEvent {
        $event = new CalendarEvent();
        $event->setUid(sprintf('supervision-%d-%d-%d-%d', $day->format('W'), $day->format('N'), $supervision->getLesson(), $supervision->isBefore()));
        $event->setAllDay(false);
        $event->setSummary($this->timetableSettings->getSupervisionLabel());

        $event->setStart($this->timetableTimeHelper->getLessonStartDateTime($day, $supervision->getLesson(), $supervision->isBefore()));
        $event->setEnd($this->timetableTimeHelper->getLessonEndDateTime($day, $supervision->getLesson(), $supervision->isBefore()));

        $teacher = $supervision->getTeacher();
        if($teacher !== null) {
            $organizer = new Organizer(new Formatter());
            $organizer->setName(sprintf('%s %s', $teacher->getFirstname(), $teacher->getLastname()));
            $organizer->setValue($teacher->getEmail());

            $event->setOrganizer($organizer);
        }

        if(!empty($supervision->getLocation())) {
            $location = new Location();
            $location->setName($supervision->getLocation());
            $event->setLocations([$location]);
        }

        return $event;
    }
}