<?php

namespace App\Controller;

use App\Date\WeekOfYear;
use App\Entity\IcsAccessToken;
use App\Entity\IcsAccessTokenType;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\Subject;
use App\Entity\TimetableLesson;
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
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Security\IcsAccessToken\IcsAccessTokenManager;
use App\Settings\TimetableSettings;
use App\Sorting\Sorter;
use App\Timetable\TimetableFilter;
use App\Timetable\TimetableHelper;
use App\Utils\ArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\RoomFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\SubjectsFilter;
use App\View\Filter\TeachersFilter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/timetable")
 */
class TimetableController extends AbstractControllerWithMessages {

    use RequestTrait;
    use CalendarWeeksTrait;

    private TimetableHelper $timetableHelper;
    private TimetableSettings $timetableSettings;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, TimetableHelper $timetableHelper, TimetableSettings $timetableSettings,
                                RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);

        $this->timetableHelper = $timetableHelper;
        $this->timetableSettings = $timetableSettings;
    }

    /**
     * @Route("", name="timetable")
     */
    public function index(StudentFilter $studentFilter, TeachersFilter $teachersFilter, GradeFilter $gradeFilter, RoomFilter $roomFilter, SubjectsFilter $subjectFilter,
                          TimetableLessonRepositoryInterface $lessonRepository, TimetableSupervisionRepositoryInterface $supervisionRepository, TimetableFilter $timetableFilter, ImportDateTypeRepositoryInterface $importDateTypeRepository,
                          SubjectRepositoryInterface $subjectRepository, SectionFilter $sectionFilter, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section', null));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $roomFilterView = $roomFilter->handle($request->query->get('room', null), $user);
        $subjectFilterView = $subjectFilter->handle($this->getArrayOrNull($request->query->get('subjects')), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $roomFilterView->getCurrentRoom() === null && count($subjectFilterView->getCurrentSubjects()) === 0);
        $teachersFilterView = $teachersFilter->handle($this->getArrayOrNull($request->query->get('teachers')), $sectionFilterView->getCurrentSection(), $user, $studentFilterView->getCurrentStudent() === null && $gradeFilterView->getCurrentGrade() === null && $roomFilterView->getCurrentRoom() === null && count($subjectFilterView->getCurrentSubjects()) === 0);

        $selectedDate = $this->resolveSelectedDate($request, $sectionFilterView->getCurrentSection(), $this->dateHelper);

        $start = max(
            $selectedDate,
            $startDate = $this->timetableSettings->getStartDate($user->getUserType())
        );
        $end = min(
            (clone $start)->modify('+13 days'),
            $endDate = $this->timetableSettings->getEndDate($user->getUserType())
        );

        $lessons = [ ];
        $supervisions = [ ];
        $membershipsTypes = [ ];

        if($start <= $end) {
            if ($studentFilterView->getCurrentStudent() !== null) {
                $lessons = $lessonRepository->findAllByStudent($start, $end, $studentFilterView->getCurrentStudent());
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
                    $lessons = array_merge($lessons, $timetableFilter->filterTeacherLessons($lessonRepository->findAllByTeacher($start, $end, $teacher)));
                    $supervisions = array_merge($supervisions, $supervisionRepository->findAllByTeacher($start, $end, $teacher));
                }
            } else if ($gradeFilterView->getCurrentGrade() !== null) {
                $lessons = $lessonRepository->findAllByGrade($start, $end, $gradeFilterView->getCurrentGrade());
                $lessons = $timetableFilter->filterGradeLessons($lessons);
            } else if ($roomFilterView->getCurrentRoom() !== null) {
                $lessons = $lessonRepository->findAllByRoom($start, $end, $roomFilterView->getCurrentRoom());
                $lessons = $timetableFilter->filterRoomLessons($lessons);
            } else if (count($subjectFilterView->getSubjects()) > 0) {
                $lessons = $lessonRepository->findAllBySubjects($start, $end, $subjectFilterView->getCurrentSubjects());
                $lessons = $timetableFilter->filterSubjectsLessons($lessons);
            }
        }

        if(count($lessons) === 0 && count($supervisions) === 0) {
            $timetable = null;
        } else {
            $weeks = [
                (new WeekOfYear((int)$start->format('Y'), (int)$start->format('W'))),
                (new WeekOfYear((int)$end->format('Y'), (int)$end->format('W')))
            ];

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

        $subjects = ArrayUtils::createArrayWithKeys(
            $subjectRepository->findAll(),
            function (Subject $subject) {
                return $subject->getAbbreviation();
            }
        );

        $weekStarts = [ ];

        if($startDate !== null && $endDate !== null && $sectionFilterView->getCurrentSection() !== null) {
            $weekStarts = $this->listCalendarWeeks(
                max($startDate, $sectionFilterView->getCurrentSection()->getStart()),
                min($endDate, $sectionFilterView->getCurrentSection()->getEnd())
            );
        }

        return $this->renderWithMessages($template, [
            'compact' => count($teachersFilterView->getCurrentTeachers()) > 1,
            'timetable' => $timetable,
            'studentFilter' => $studentFilterView,
            'teachersFilter' => $teachersFilterView,
            'gradeFilter' => $gradeFilterView,
            'roomFilter'=> $roomFilterView,
            'subjectFilter' => $subjectFilterView,
            'sectionFilter' => $sectionFilterView,
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
            'subjects' => $subjects,
            'selectedDate' => $selectedDate,
            'weekStarts' => $weekStarts
        ]);
    }

    /**
     * @Route("/export", name="timetable_export")
     */
    public function export(Request $request, IcsAccessTokenManager $manager) {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new IcsAccessToken())
            ->setType(IcsAccessTokenType::Timetable())
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

    protected function getMessageScope(): MessageScope {
        return MessageScope::Timetable();
    }
}