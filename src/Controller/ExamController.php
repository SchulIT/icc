<?php

namespace App\Controller;

use App\Dashboard\Absence\AbsenceResolver;
use App\Dashboard\Absence\ExamStudentsResolver;
use App\Dashboard\AbsentStudent;
use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Entity\IcsAccessToken;
use App\Entity\IcsAccessTokenType;
use App\Entity\MessageScope;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\User;
use App\Export\ExamIcsExporter;
use App\Form\IcsAccessTokenType as DeviceTokenTypeForm;
use App\Grouping\ExamDateStrategy;
use App\Grouping\ExamStudentTuitionStrategy;
use App\Grouping\ExamWeekStrategy;
use App\Grouping\Grouper;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Security\IcsAccessToken\IcsAccessTokenManager;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\StudentAbsenceVoter;
use App\Settings\ExamSettings;
use App\Sorting\ExamDateLessonStrategy as ExamDateSortingStrategy;
use App\Sorting\ExamStudentStrategy;
use App\Sorting\ExamStudentTuitionGroupStrategy;
use App\Sorting\ExamWeekGroupStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/exams')]
class ExamController extends AbstractControllerWithMessages {

    private static int $ItemsPerPage = 25;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper, private ImportDateTypeRepositoryInterface $importDateTypeRepository,
                                DateHelper $dateHelper, private Grouper $grouper, private Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);
    }

    #[Route(path: '', name: 'exams')]
    public function index(SectionFilter $sectionFilter, TeacherFilter $teacherFilter, StudentFilter $studentsFilter, GradeFilter $gradeFilter, StudyGroupFilter $studyGroupFilter,
                          ExamRepositoryInterface $examRepository, ExamSettings $examSettings, Request $request, DateHelper $dateHelper): Response {
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

    private function isVisibleForGrade(User $user, ExamSettings $examSettings, ?Section $section): bool {
        if($user->isStudentOrParent() === false) {
            return true;
        }

        if($section === null) {
            return false;
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

    #[Route(path: '/export', name: 'exams_export')]
    public function export(Request $request, IcsAccessTokenManager $manager): Response {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new IcsAccessToken())
            ->setType(IcsAccessTokenType::Exams)
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

    #[Route(path: '/ics/download', name: 'exams_ics')]
    #[Route(path: '/ics/download/{token}', name: 'exams_ics_token')]
    public function ics(ExamIcsExporter $exporter): Response {
        /** @var User $user */
        $user = $this->getUser();

        return $exporter->getIcsResponse($user);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Exams;
    }

    #[Route(path: '/{uuid}', name: 'show_exam', requirements: ['id' => '\d+'])]
    public function show(Exam $exam, AbsenceResolver $absenceResolver, ExamRepositoryInterface $repository): Response {
        $this->denyAccessUnlessGranted(ExamVoter::Show, $exam);

        /** @var Student[] $students */
        $students = $exam->getStudents()->map(fn(ExamStudent $es) => $es->getStudent())->toArray();

        $absentStudents = [ ];
        if($this->isGranted(StudentAbsenceVoter::CanViewAny)) {
            $absentStudents = ArrayUtils::createArrayWithKeys(
                $absenceResolver->resolve(
                    $exam->getDate(),
                    $exam->getLessonStart(),
                    $students,
                    [
                        ExamStudentsResolver::class
                    ]
                ),
                fn(AbsentStudent $student) => $student->getStudent()->getUuid()->toString()
            );
        }

        $groups = $this->grouper->group($exam->getStudents()->toArray(), ExamStudentTuitionStrategy::class);
        $this->sorter->sort($groups, ExamStudentTuitionGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, ExamStudentStrategy::class);

        $relatedExams = [ ];

        if($exam->getRoom() !== null) {
            for ($lesson = $exam->getLessonStart(); $lesson <= $exam->getLessonEnd(); $lesson++) {
                $relatedExams = array_merge($relatedExams, $repository->findAllByRoomAndDateAndLesson($exam->getRoom(), $exam->getDate(), $lesson));
            }
        }

        $relatedExams = array_filter($relatedExams, fn(Exam $e) => $e->getId() !== $exam->getId());

        return $this->renderWithMessages('exams/details.html.twig', [
            'exam' => $exam,
            'groups' => $groups,
            'absentStudents' => $absentStudents,
            'relatedExams' => ArrayUtils::unique($relatedExams),
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }
}