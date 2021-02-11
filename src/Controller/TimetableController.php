<?php

namespace App\Controller;

use App\Entity\IcsAccessToken;
use App\Entity\IcsAccessTokenType;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\Subject;
use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableSupervision;
use App\Entity\User;
use App\Export\TimetableIcsExporter;
use App\Form\IcsAccessTokenType as DeviceTokenTypeForm;
use App\Grouping\Grouper;
use App\Message\DismissedMessagesHelper;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Security\IcsAccessToken\IcsAccessTokenManager;
use App\Security\Voter\TimetablePeriodVoter;
use App\Settings\TimetableSettings;
use App\Sorting\Sorter;
use App\Sorting\TimetablePeriodStrategy;
use App\Timetable\TimetableFilter;
use App\Timetable\TimetableHelper;
use App\Utils\ArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\RoomFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\SubjectsFilter;
use App\View\Filter\TeachersFilter;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/timetable")
 */
class TimetableController extends AbstractControllerWithMessages {

    private $timetableHelper;
    private $timetableSettings;
    private $grouper;
    private $sorter;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, TimetableHelper $timetableHelper, TimetableSettings $timetableSettings,
                                Grouper $grouper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);

        $this->timetableHelper = $timetableHelper;
        $this->timetableSettings = $timetableSettings;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("", name="timetable")
     */
    public function index(StudentFilter $studentFilter, TeachersFilter $teachersFilter, GradeFilter $gradeFilter, RoomFilter $roomFilter, SubjectsFilter $subjectFilter,
                          TimetableWeekRepositoryInterface $weekRepository, TimetableLessonRepositoryInterface $lessonRepository, TimetablePeriodRepositoryInterface $periodRepository,
                          TimetableSupervisionRepositoryInterface $supervisionRepository, TimetableFilter $timetableFilter, ImportDateTypeRepositoryInterface $importDateTypeRepository,
                          SubjectRepositoryInterface $subjectRepository, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $user);
        $roomFilterView = $roomFilter->handle($request->query->get('room', null), $user);
        $subjectFilterView = $subjectFilter->handle($request->query->get('subjects', [ ]), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $user, $gradeFilterView->getCurrentGrade() === null && $roomFilterView->getCurrentRoom() === null && count($subjectFilterView->getCurrentSubjects()) === 0);
        $teachersFilterView = $teachersFilter->handle($request->query->get('teachers', []), $user, $studentFilterView->getCurrentStudent() === null && $gradeFilterView->getCurrentGrade() === null && $roomFilterView->getCurrentRoom() === null && count($subjectFilterView->getCurrentSubjects()) === 0);

        $periods = $periodRepository->findAll();
        $periods = array_filter($periods, function(TimetablePeriod $period) {
            return $this->isGranted(TimetablePeriodVoter::View, $period);
        });

        $this->sorter->sort($periods, TimetablePeriodStrategy::class);

        $currentPeriod = $this->getCurrentPeriod($periods);

        if($request->query->get('period') !== null) {
            foreach($periods as $period) {
                if($period->getUuid()->toString() === $request->query->get('period')) {
                    $currentPeriod = $period;
                }
            }
        }

        $weeks = $weekRepository->findAll();

        $lessons = [ ];
        $supervisions = [ ];
        $membershipsTypes = [ ];

        if($currentPeriod !== null) {
            if ($studentFilterView->getCurrentStudent() !== null) {
                $lessons = $lessonRepository->findAllByPeriodAndStudent($currentPeriod, $studentFilterView->getCurrentStudent());
                $lessons = $timetableFilter->filterStudentLessons($lessons);

                $gradeIdsWithMembershipTypes = $this->timetableSettings->getGradeIdsWithMembershipTypes();

                /** @var StudyGroupMembership $membership */
                foreach($studentFilterView->getCurrentStudent()->getStudyGroupMemberships() as $membership) {
                    foreach($membership->getStudyGroup()->getGrades() as $grade) {
                        if (in_array($grade->getId(), $gradeIdsWithMembershipTypes)) {
                            $membershipsTypes[$membership->getStudyGroup()->getId()] = $membership->getType();
                        }
                    }
                }
            } else if (count($teachersFilterView->getCurrentTeachers()) > 0) {
                $lessons = [ ];
                $supervisions = [ ];

                foreach($teachersFilterView->getCurrentTeachers() as $teacher) {
                    $lessons = array_merge($lessons, $timetableFilter->filterTeacherLessons($lessonRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher)));
                    $supervisions = array_merge($supervisions, $supervisionRepository->findAllByPeriodAndTeacher($currentPeriod, $teacher));
                }
            } else if ($gradeFilterView->getCurrentGrade() !== null) {
                $lessons = $lessonRepository->findAllByPeriodAndGrade($currentPeriod, $gradeFilterView->getCurrentGrade());
                $lessons = $timetableFilter->filterGradeLessons($lessons);
            } else if ($roomFilterView->getCurrentRoom() !== null) {
                $lessons = $lessonRepository->findAllByPeriodAndRoom($currentPeriod, $roomFilterView->getCurrentRoom());
                $lessons = $timetableFilter->filterRoomLessons($lessons);
            } else if (count($subjectFilterView->getSubjects()) > 0) {
                $lessons = $lessonRepository->findAllByPeriodAndSubjects($currentPeriod, $subjectFilterView->getCurrentSubjects());
                $lessons = $timetableFilter->filterSubjectsLessons($lessons);
            }
        }

        if(count($lessons) === 0 && count($supervisions) === 0) {
            $timetable = null;
        } else {
            $timetable = $this->timetableHelper->makeTimetable($weeks, $lessons, $supervisions);
        }

        $startTimes = [ ];
        $endTimes = [ ];

        for($lesson = 1; $lesson <= $this->timetableSettings->getMaxLessons(); $lesson++) {
            $startTimes[$lesson] = $this->timetableSettings->getStart($lesson);
            $endTimes[$lesson] = $this->timetableSettings->getEnd($lesson);
        }

        $template = 'timetable/index.html.twig';

        if($request->query->getBoolean('print', false) === true) {
            $template = 'timetable/index_print.html.twig';

            if($timetable === null) {
                $query = $request->query->all();
                unset($query['print']);
                $this->addFlash('info', 'plans.timetable.print.empty');
                return $this->redirectToRoute('timetable', $query);
            }
        }

        $supervisionLabels = [ ];
        for($i = 1; $i <= $this->timetableSettings->getMaxLessons(); $i++) {
            $supervisionLabels[$i] = $this->timetableSettings->getDescriptionBeforeLesson($i);
        }

        $nextPeriod = null;
        $previousPeriod = null;

        if($currentPeriod !== null) {
            // Search previous and next period (if any)
            $periodIdx = null;

            for($idx = 0; $idx < count($periods); $idx++) {
                if($currentPeriod->getUuid() === $periods[$idx]->getUuid()) {
                    $periodIdx = $idx;
                    break;
                }
            }

            if($periodIdx !== null) {
                $nextPeriod = $periods[$periodIdx + 1] ?? null;
                $previousPeriod = $periods[$periodIdx - 1] ?? null;
            }
        }

        $subjects = ArrayUtils::createArrayWithKeys(
            $subjectRepository->findAll(),
            function (Subject $subject) {
                return $subject->getAbbreviation();
            }
        );

        return $this->renderWithMessages($template, [
            'compact' => count($teachersFilterView->getCurrentTeachers()) > 1,
            'timetable' => $timetable,
            'studentFilter' => $studentFilterView,
            'teachersFilter' => $teachersFilterView,
            'gradeFilter' => $gradeFilterView,
            'roomFilter'=> $roomFilterView,
            'subjectFilter' => $subjectFilterView,
            'periods' => $periods,
            'currentPeriod' => $currentPeriod,
            'nextPeriod' => $nextPeriod,
            'previousPeriod' => $previousPeriod,
            'startTimes' => $startTimes,
            'endTimes' => $endTimes,
            'gradesWithCourseNames' => $this->timetableSettings->getGradeIdsWithCourseNames(),
            'memberships' => $membershipsTypes,
            'query' => $request->query->all(),
            'supervisionLabels' => $supervisionLabels,
            'supervisionSubject' => $this->timetableSettings->getSupervisionLabel(),
            'supervisionColor' => $this->timetableSettings->getSupervisionColor(),
            'last_import_lessons' => $importDateTypeRepository->findOneByEntityClass(TimetableLesson::class),
            'last_import_supervisions' => $importDateTypeRepository->findOneByEntityClass(TimetableSupervision::class),
            'subjects' => $subjects
        ]);
    }

    /**
     * @Route("/export", name="timetable_export")
     */
    public function export(Request $request, IcsAccessTokenManager $manager) {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new IcsAccessToken())
            ->setType(IcsAccessTokenType::Calendar())
            ->setUser($user);

        $form = $this->createForm(DeviceTokenTypeForm::class, $deviceToken);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $deviceToken = $manager->persistToken($deviceToken);
        }

        return $this->renderWithMessages('timetable/export.html.twig', [
            'form' => $form->createView(),
            'token' => $deviceToken
        ]);
    }

    /**
     * @Route("/ics/download", name="timetable_ics")
     * @Route("/ics/downloads/{token}", name="timetable_ics_token")
     */
    public function ics(TimetableIcsExporter $icsExporter) {
        /** @var User $user */
        $user = $this->getUser();

        return $icsExporter->getIcsResponse($user);
    }

    /**
     * @param TimetablePeriod[] $periods
     * @return TimetablePeriod|null
     */
    private function getCurrentPeriod(array $periods): ?TimetablePeriod {
        foreach($periods as $period) {
            if($this->dateHelper->isBetween($this->dateHelper->getToday(), $period->getStart(), $period->getEnd())) {
                return $period;
            }
        }

        if(count($periods) > 0) {
            return $periods[count($periods) - 1];
        }

        return null;
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Timetable();
    }
}