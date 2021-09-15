<?php

namespace App\Export;

use App\Entity\Exam;
use App\Entity\ExamSupervision;
use App\Entity\Grade;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Ics\IcsHelper;
use App\Repository\ExamRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Settings\ExamSettings;
use App\Timetable\TimetableTimeHelper;
use i;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExamIcsExporter {
    private $examSettings;
    private $examRepository;
    private $timetableTimeHelper;
    private $icsHelper;
    private $translator;
    private $authorizationChecker;

    public function __construct(ExamSettings $examSettings, ExamRepositoryInterface $examRepository, TimetableTimeHelper $timetableTimeHelper,
                                IcsHelper $icsHelper, TranslatorInterface $translator, AuthorizationCheckerInterface $authorizationChecker) {
        $this->examSettings = $examSettings;
        $this->examRepository = $examRepository;
        $this->timetableTimeHelper = $timetableTimeHelper;
        $this->icsHelper = $icsHelper;
        $this->translator = $translator;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getIcsResponse(User $user): Response {
        return $this->icsHelper->getIcsResponse(
            $this->translator->trans('exams.export.title'),
            $this->translator->trans('exams.export.description', [ '%user%' => $user->getUsername() ]),
            $this->getIcsItems($user),
            $this->translator->trans('plans.exams.export.download.filename')
        );
    }

    /**
     * @param User $user
     * @return CalendarEvent[]
     */
    private function getIcsItems(User $user) {
        if($this->examSettings->isVisibileFor($user->getUserType()) === false) {
            return [ ];
        }

        $exams = [ ];

        if($user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent())) {
            $exams = $this->examRepository->findAllByStudents($user->getStudents()->toArray(), null, false, true);
        } else if($user->getUserType()->equals(UserType::Teacher())) {
            $exams = $this->examRepository->findAllByTeacher($user->getTeacher(), null, false, true);
        }

        $exams = array_filter($exams, function(Exam $exam) {
            return $this->authorizationChecker->isGranted(ExamVoter::Show, $exam);
        });

        $items = [ ];

        foreach($exams as $exam) {
            $items = array_merge($items, $this->makeIcsItems($exam, $user));
        }

        return $items;
    }

    /**
     * @param Exam $exam
     * @param User $user
     * @return CalendarEvent[]
     */
    private function makeIcsItems(Exam $exam, User $user) {
        if($user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent())) {
            return [ $this->makeIcsItem($exam) ];
        }

        $items = [ ];

        if($this->isExamTeacher($exam, $user->getTeacher())) {
            $items[] = $this->makeIcsItem($exam);
        }

        if($user->getTeacher() !== null) {
            /** @var ExamSupervision[] $supervisions */
            $supervisions = $exam->getSupervisions();

            foreach($supervisions as $supervision) {
                if($supervision->getTeacher()->getId() === $user->getTeacher()->getId()) {
                    $items[] = $this->makeIcsItemSupervision($exam,$exam->getLessonStart() + $supervision->getLesson() - 1);
                }
            }
        }

        return $items;
    }

    private function makeIcsItem(Exam $exam): CalendarEvent {
        $start = $this->getDateTime($exam->getDate(), $this->timetableTimeHelper->getLessonStartDateTime($exam->getDate(), $exam->getLessonStart()));
        $end = $this->getDateTime($exam->getDate(), $this->timetableTimeHelper->getLessonEndDateTime($exam->getDate(), $exam->getLessonEnd()));
        $description = $this->translator->trans('exams.export.exam_description', [
            '%tuitions%' => $this->getTuitionsAsString($exam->getTuitions()->toArray())
        ]);

        $event = (new CalendarEvent())
            ->setUid(sprintf('exam-%d', $exam->getId()))
            ->setSummary($description)
            ->setDescription($description)
            ->setStart($start)
            ->setEnd($end);

        if($exam->getRoom() !== null) {
            $event->setLocations([
                (new Location())
                    ->setName($exam->getRoom()->getName())
            ]);
        }

        return $event;
    }

    private function makeIcsItemSupervision(Exam $exam, int $lesson): CalendarEvent {
        $start = $this->getDateTime($exam->getDate(), $this->timetableTimeHelper->getLessonStartDateTime($exam->getDate(), $lesson));
        $end = $this->getDateTime($exam->getDate(), $this->timetableTimeHelper->getLessonEndDateTime($exam->getDate(), $lesson));
        $description = $this->translator->trans('exams.export.supervision_description', [
            '%tuitions%' => $this->getTuitionsAsString($exam->getTuitions()->toArray())
        ]);

        $event = (new CalendarEvent())
            ->setUid(sprintf('exam-%d-supervision-%d', $exam->getId(), $lesson))
            ->setSummary($description)
            ->setDescription($description)
            ->setStart($start)
            ->setEnd($end);


        if($exam->getRoom() !== null) {
            $event->setLocations([
                (new Location())
                    ->setName($exam->getRoom()->getName())
            ]);
        }

        return $event;
    }

    private function isExamTeacher(Exam $exam, ?Teacher $teacher): bool {
        if($teacher === null) {
            return false;
        }

        /** @var Tuition $tuition */
        foreach($exam->getTuitions() as $tuition) {
            foreach($tuition->getTeachers() as $currentTeacher) {
                if($currentTeacher->getId() === $teacher->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Tuition[] $tuitions
     * @return string
     */
    private function getTuitionsAsString($tuitions): string {
        $strings = [ ];

        foreach($tuitions as $tuition) {
            $grades = [ ];

            foreach($tuition->getStudyGroup()->getGrades() as $grade) {
                $grades[$grade->getId()] = $grade;
            }

            $strings[] = $this->translator->trans('exams.export.tuition', [
                '%name%' => $tuition->getName(),
                '%grades%' => $this->getGradesAsString($grades)
            ]);
        }

        return implode(',', $strings);
    }

    /**
     * @param Grade[] $grades
     * @return string
     */
    private function getGradesAsString($grades): string {
        return implode(', ', array_map(function(Grade $grade) {
            return $grade->getName();
        }, $grades));
    }

    /**
     * @param string[] $rooms
     * @return string
     */
    private function getRoomsAsString($rooms): string {
        return implode(', ', $rooms);
    }

    private function getDateTime(\DateTime $day, \DateTime $time) {
        $dateString = sprintf('%s %s:00', $day->format('Y-m-d'), $time->format('H:i'));
        return new \DateTime($dateString);
    }
}