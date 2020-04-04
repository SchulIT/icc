<?php

namespace App\Export;

use App\Entity\Exam;
use App\Entity\ExamInvigilator;
use App\Entity\Grade;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Ics\IcsHelper;
use App\Repository\ExamRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Settings\ExamSettings;
use App\Settings\TimetableSettings;
use Jsvrcek\ICS\Model\CalendarEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExamIcsExporter {
    private $examSettings;
    private $examRepository;
    private $timetableSettings;
    private $icsHelper;
    private $translator;
    private $authorizationChecker;

    public function __construct(ExamSettings $examSettings, ExamRepositoryInterface $examRepository, TimetableSettings $timetableSettings,
                                IcsHelper $icsHelper, TranslatorInterface $translator, AuthorizationCheckerInterface $authorizationChecker) {
        $this->examSettings = $examSettings;
        $this->examRepository = $examRepository;
        $this->timetableSettings = $timetableSettings;
        $this->icsHelper = $icsHelper;
        $this->translator = $translator;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getIcsResponse(User $user): Response {
        return $this->icsHelper->getIcsResponse(
            $this->translator->trans('exams.export.title'),
            $this->translator->trans('exams.export.description', [ '%user%' => $user->getUsername() ]),
            $this->getIcsItems($user)
        );
    }

    /**
     * @param User $user
     * @return CalendarEvent[]
     */
    private function getIcsItems(User $user) {
        if($this->examSettings->isVisibileFor($user->getUserType())) {
            return [ ];
        }

        $exams = [ ];

        if($user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent())) {
            $exams = $this->examRepository->findAllByStudents($user->getStudents()->toArray());
        } else if($user->getUserType()->equals(UserType::Teacher())) {
            $exams = $this->examRepository->findAllByTeacher($user->getTeacher());
        }

        $exams = array_filter($exams, function(Exam $exam) {
            return $this->authorizationChecker->isGranted(ExamVoter::Show, $exam);
        });

        $items = [ ];

        foreach($exams as $exam) {
            $items += $this->makeIcsItems($exam, $user);
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
            /** @var ExamInvigilator[] $invigilators */
            $invigilators = $exam->getInvigilators();

            foreach($invigilators as $invigilator) {
                if($invigilator->getTeacher()->getId() === $user->getTeacher()->getId()) {
                    $items[] = $this->makeIcsItemInvigilator($exam,$exam->getLessonStart() + $invigilator->getLesson() - 1);
                }
            }
        }

        return $items;
    }

    private function makeIcsItem(Exam $exam): CalendarEvent {
        $start = $this->getDateTime($exam->getDate(), $this->timetableSettings->getStart($exam->getLessonStart()));
        $end = $this->getDateTime($exam->getDate(), $this->timetableSettings->getEnd($exam->getLessonEnd()));
        $description = $this->translator->trans('exams.export.exam_description', [
            '%tuitions%' => $this->getTuitionsAsString($exam->getTuitions()->toArray())
        ]);

        return (new CalendarEvent())
            ->setUid(sprintf('exam-%d', $exam->getId()))
            ->setSummary($description)
            ->setDescription($description)
            ->setStart($start)
            ->setEnd($end)
            ->setLocations($exam->getRooms());
    }

    private function makeIcsItemInvigilator(Exam $exam, int $lesson): CalendarEvent {
        $start = $this->getDateTime($exam->getDate(), $this->timetableSettings->getStart($lesson));
        $end = $this->getDateTime($exam->getDate(), $this->timetableSettings->getEnd($lesson));
        $description = $this->translator->trans('exams.export.invigilator_description', [
            '%tuitions%' => $this->getTuitionsAsString($exam->getTuitions()->toArray())
        ]);

        return (new CalendarEvent())
            ->setUid(sprintf('exam-%d-invigilator-%d', $exam->getId(), $lesson))
            ->setSummary($description)
            ->setDescription($description)
            ->setStart($start)
            ->setEnd($end)
            ->setLocations($exam->getRooms());
    }

    private function isExamTeacher(Exam $exam, ?Teacher $teacher): bool {
        if($teacher === null) {
            return false;
        }

        foreach($exam->getTuitions() as $tuition) {
            if($tuition->getTeacher()->getId() === $teacher->getId()) {
                return true;
            }

            foreach($tuition->getAdditionalTeachers() as $additionalTeacher) {
                if($additionalTeacher->getId() === $teacher->getId()) {
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