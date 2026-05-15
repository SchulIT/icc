<?php

namespace App\Exam\Controller;

use App\Framework\Controller\AbstractControllerWithMessages;
use App\Dashboard\Absence\AbsenceResolver;
use App\Dashboard\Absence\ExamStudentsResolver;
use App\Dashboard\AbsentStudent;
use App\Exam\Entity\Exam;
use App\Exam\Entity\ExamStudent;
use App\Common\Entity\IcsAccessToken;
use App\Common\Entity\IcsAccessTokenType;
use App\Message\Entity\MessageScope;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Entity\StudentInformation;
use App\Common\Entity\StudentInformationType;
use App\Common\Entity\User;
use App\Exam\Export\ExamIcsExporter;
use App\Form\IcsAccessTokenType as DeviceTokenTypeForm;
use App\Exam\Grouping\ExamDateStrategy;
use App\Exam\Grouping\ExamStudentTuitionStrategy;
use App\Exam\Grouping\ExamWeekStrategy;
use App\Framework\Grouping\Grouper;
use App\Message\DismissedMessagesHelper;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Framework\Import\Repository\ImportDateTypeRepositoryInterface;
use App\Message\Repository\MessageRepositoryInterface;
use App\Common\Repository\StudentInformationRepositoryInterface;
use App\Common\Security\IcsAccessToken\IcsAccessTokenManager;
use App\Common\Voter\StudentInformationVoter;
use App\Exam\Voter\ExamVoter;
use App\StudentAbsence\Voter\StudentAbsenceVoter;
use App\Exam\Settings\ExamSettings;
use App\Exam\Sorting\ExamDateLessonStrategy as ExamDateSortingStrategy;
use App\Exam\Sorting\ExamStudentStrategy;
use App\Exam\Sorting\ExamStudentTuitionGroupStrategy;
use App\Exam\Sorting\ExamWeekGroupStrategy;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudentStrategy;
use App\Framework\Utils\ArrayUtils;
use App\Framework\Utils\EnumArrayUtils;
use App\Common\View\Filter\GradeFilter;
use App\Common\View\Filter\SectionFilter;
use App\Common\View\Filter\StudentFilter;
use App\Common\View\Filter\StudyGroupFilter;
use App\Common\View\Filter\TeacherFilter;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


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
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Exam $exam, AbsenceResolver $absenceResolver, ExamRepositoryInterface $repository, StudentInformationRepositoryInterface $studentInformationRepository): Response {
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

        $studentInformation = [ ];
        $studentInformation = ArrayUtils::createArrayWithKeys(
            array_filter(
                $studentInformationRepository->findByStudents($students, StudentInformationType::Exams, $exam->getDate(), $exam->getDate()),
                fn(StudentInformation $information) => $this->isGranted(StudentInformationVoter::Show, $information)
            ),
            fn(StudentInformation $information) => $information->getStudent()->getUuid()->toString(),
            true
        );

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
            'studentInformation' => $studentInformation,
            'relatedExams' => ArrayUtils::unique($relatedExams),
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }
}