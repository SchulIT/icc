<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\IcsAccessToken;
use App\Entity\IcsAccessTokenType;
use App\Entity\MessageScope;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Export\ExamIcsExporter;
use App\Form\IcsAccessTokenType as DeviceTokenTypeForm;
use App\Grouping\ExamWeekGroup;
use App\Grouping\ExamWeekStrategy;
use App\Grouping\Grouper;
use App\Grouping\WeekOfYear;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Security\IcsAccessToken\IcsAccessTokenManager;
use App\Security\Voter\ExamVoter;
use App\Settings\ExamSettings;
use App\Sorting\ExamDateLessonStrategy as ExamDateSortingStrategy;
use App\Sorting\ExamWeekGroupStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/exams")
 */
class ExamController extends AbstractControllerWithMessages {

    private static int $ItemsPerPage = 25;

    private Grouper $grouper;
    private Sorter $sorter;

    private ImportDateTypeRepositoryInterface $importDateTypeRepository;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper, ImportDateTypeRepositoryInterface $importDateTypeRepository,
                                DateHelper $dateHelper, Grouper $grouper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);

        $this->grouper = $grouper;
        $this->sorter = $sorter;
        $this->importDateTypeRepository = $importDateTypeRepository;
    }

    /**
     * @Route("", name="exams")
     */
    public function index(SectionFilter $sectionFilter, TeacherFilter $teacherFilter, StudentFilter $studentsFilter, GradeFilter $gradeFilter, StudyGroupFilter $studyGroupFilter,
                          ExamRepositoryInterface $examRepository, ExamSettings $examSettings, Request $request, DateHelper $dateHelper) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group', null), $sectionFilterView->getCurrentSection(), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentsFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user, $studyGroupFilterView->getCurrentStudyGroup() === null && $gradeFilterView->getCurrentGrade() === null);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $request->query->get('teacher') !== '✗' && $studyGroupFilterView->getCurrentStudyGroup() === null && $gradeFilterView->getCurrentGrade() === null && $studentFilterView->getCurrentStudent() === null);
        $includePastExams = $request->query->getBoolean('past', false);

        $isVisible = $examSettings->isVisibileFor($user->getUserType()) && $this->isVisibleForGrade($user, $examSettings, $sectionFilterView->getCurrentSection());
        $isVisibleAdmin = false;

        $groups = [ ];
        $exams = [ ];

        $page = $request->query->getInt('page', 1);
        $pages = 1;

        if($isVisible === true || $this->isGranted('ROLE_EXAMS_CREATOR') || $this->isGranted('ROLE_EXAMS_ADMIN')) {
            if($isVisible === false) {
                $isVisibleAdmin = $this->isGranted('ROLE_EXAMS_CREATOR') || $this->isGranted('ROLE_EXAMS_ADMIN');
            }

            $threshold = $includePastExams ? null : $this->dateHelper->getToday();
            $paginator = null;

            if ($studentFilterView->getCurrentStudent() !== null) {
                $paginator = $examRepository->getPaginator(self::$ItemsPerPage, $page, null, null, $studentFilterView->getCurrentStudent(), null, true, $threshold);
            } else if ($studyGroupFilterView->getCurrentStudyGroup() !== null) {
                $paginator = $examRepository->getPaginator(self::$ItemsPerPage, $page, null, null, null, $studyGroupFilterView->getCurrentStudyGroup(), true, $threshold);
            } else if ($gradeFilterView->getCurrentGrade() !== null) {
                $paginator = $examRepository->getPaginator(self::$ItemsPerPage, $page, $gradeFilterView->getCurrentGrade(), null, null, null, true, $threshold);
            } else if ($teacherFilterView->getCurrentTeacher() !== null) {
                $paginator = $examRepository->getPaginator(self::$ItemsPerPage, $page, null, $teacherFilterView->getCurrentTeacher(), null, null, true, $threshold);
            } else {
                $paginator = $examRepository->getPaginator(self::$ItemsPerPage, $page, null, null, null, null, true, $threshold);
            }

            if($paginator !== null) {
                /** @var Exam $exam */
                foreach ($paginator->getIterator() as $exam) {
                    if ($this->isGranted(ExamVoter::Show, $exam)) {
                        $exams[] = $exam;
                    }
                }

                if($paginator->count() > 0) {
                    $pages = ceil((float)$paginator->count() / self::$ItemsPerPage);
                }
            }
        }

        $groups = $this->grouper->group($exams, ExamWeekStrategy::class);
        $this->sorter->sort($groups, ExamWeekGroupStrategy::class);
        $this->sorter->sortGroupItems($exams, ExamDateSortingStrategy::class);

        return $this->renderWithMessages('exams/index.html.twig', [
            'examWeekGroups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'gradeFilter' => $gradeFilterView,
            'studyGroupFilter' => $studyGroupFilterView,
            'isVisible' => $isVisible,
            'isVisibleAdmin' => $isVisibleAdmin,
            'includePastExams' => $includePastExams,
            'pages' => $pages,
            'page' => $page,
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }

    private function isVisibleForGrade(User $user, ExamSettings $examSettings, Section $section) {
        if(EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent()]) === false) {
            return true;
        }

        $visibleGradeIds = $examSettings->getVisibleGradeIds();
        $gradeIds = [ ];

        /** @var Student $student */
        foreach($user->getStudents() as $student) {
            $grade = $student->getGrade($section);

            if($grade !== null) {
                $gradeIds[] = $grade->getId();
            }
        }

        return count(array_intersect($visibleGradeIds, $gradeIds)) > 0;
    }

    private function getExams(?ExamWeekGroup $group, \Closure $repositoryCall) {
        if($group === null) {
            return [];
        }

        $date = clone $group->getWeekOfYear()->getFirstDay();
        $exams = [ ];

        while($date <= $group->getWeekOfYear()->getLastDay()) {
            $exams = array_merge($exams, $repositoryCall($date));

            $date = $date->modify('+1 day');
        }

        return $exams;
    }

    /**
     * @param ExamWeekGroup[] $groups
     * @param int|null $year
     * @param int|null $weekNumber
     * @param DateHelper $dateHelper
     * @return ExamWeekGroup|null
     */
    private function getCurrentGroup(array $groups, ?int $year, ?int $weekNumber, DateHelper $dateHelper): ?ExamWeekGroup {
        if ($year === null || $weekNumber === null) {
            $today = $dateHelper->getToday();
            $weekNumber = (int)$today->format('W');
            $year = (int)$today->format('Y');
        }

        $currentGroup = null;

        foreach ($groups as $group) {
            if ($group->getWeekOfYear()->getYear() >= $year && $group->getWeekOfYear()->getWeekNumber() >= $weekNumber) {
                $currentGroup = $group;

                if ($group->getWeekOfYear()->getYear() === $year && $group->getWeekOfYear()->getWeekNumber() === $weekNumber) {
                    break;
                }
            }
        }

        return $currentGroup;
    }

    private function computeGroups(array $examInfo) {
        $groups = [ ];

        foreach($examInfo as $info) {
            $date = new DateTime($info['date']);
            $count = intval($info['count']);

            $weekNumber = (int)$date->format('W');
            $year = (int)$date->format('Y');

            $key = sprintf('%d-%d', $year, $weekNumber);

            if(!array_key_exists($key, $groups)) {
                $groups[$key] = new ExamWeekGroup(new WeekOfYear($year, $weekNumber));
            }

            // Add fake counter
            for($i = 0; $i < $count; $i++) {
                $groups[$key]->addItem(new Exam());
            }
        }

        return array_values($groups);
    }

    /**
     * @Route("/export", name="exams_export")
     */
    public function export(Request $request, IcsAccessTokenManager $manager) {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new IcsAccessToken())
            ->setType(IcsAccessTokenType::Exams())
            ->setUser($user);

        $form = $this->createForm(DeviceTokenTypeForm::class, $deviceToken);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $deviceToken = $manager->persistToken($deviceToken);
        }

        return $this->renderWithMessages('exams/export.html.twig', [
            'form' => $form->createView(),
            'token' => $deviceToken
        ]);
    }

    /**
     * @Route("/ics/download", name="exams_ics")
     * @Route("/ics/download/{token}", name="exams_ics_token")
     */
    public function ics(ExamIcsExporter $exporter) {
        /** @var User $user */
        $user = $this->getUser();

        return $exporter->getIcsResponse($user);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Exams();
    }

    /**
     * @Route("/{uuid}", name="show_exam", requirements={"id": "\d+"})
     */
    public function show(Exam $exam) {
        $this->denyAccessUnlessGranted(ExamVoter::Show, $exam);

        $studyGroups = [ ];
        /** @var Student[] $students */
        $students = $exam->getStudents()->toArray();

        $this->sorter->sort($students, StudentStrategy::class);

        return $this->renderWithMessages('exams/details.html.twig', [
            'exam' => $exam,
            'students' => $students,
            'studyGroups' => $studyGroups,
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }
}