<?php

namespace App\Controller;

use App\Entity\DeviceToken;
use App\Entity\DeviceTokenType;
use App\Entity\Exam;
use App\Entity\MessageScope;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Export\ExamIcsExporter;
use App\Form\DeviceTokenType as DeviceTokenTypeForm;
use App\Grouping\ExamDateStrategy;
use App\Grouping\ExamWeekGroup;
use App\Grouping\ExamWeekStrategy;
use App\Grouping\Grouper;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Security\Devices\DeviceManager;
use App\Security\Voter\ExamVoter;
use App\Settings\ExamSettings;
use App\Sorting\ExamDateGroupStrategy;
use App\Sorting\ExamDateLessonStrategy as ExamDateSortingStrategy;
use App\Sorting\ExamWeekGroupStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use SchoolIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/exams")
 */
class ExamController extends AbstractControllerWithMessages {

    private $grouper;
    private $sorter;

    private $importDateTypeRepository;

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
    public function index(TeacherFilter $teacherFilter, StudentFilter $studentsFilter, GradeFilter $gradeFilter, StudyGroupFilter $studyGroupFilter,
                          ExamRepositoryInterface $examRepository, ExamSettings $examSettings, Request $request, DateHelper $dateHelper) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        $all = $request->query->get('all', false) === '✓';
        $studentFilterView = $studentsFilter->handle($request->query->get('student', null), $user);
        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group', null), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, false);

        $exams = [ ];
        $today = $all ? null : $this->dateHelper->getToday();

        $isVisible = $examSettings->isVisibileFor($user->getUserType());
        $isVisibleAdmin = false;

        if($isVisible === true || $this->isGranted('ROLE_EXAMS_CREATOR') || $this->isGranted('ROLE_EXAMS_ADMIN')) {
            $isVisible = true;
            $isVisibleAdmin = true;

            if ($studentFilterView->getCurrentStudent() !== null) {
                $exams = $examRepository->findAllByStudents([$studentFilterView->getCurrentStudent()], $today);
            } else if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
                $exams = $examRepository->findAllByStudyGroup($studyGroupFilterView->getCurrentStudyGroup());
            } else if ($gradeFilterView->getCurrentGrade() !== null) {
                    $exams = $examRepository->findAllByGrade($gradeFilterView->getCurrentGrade(), $today);
            } else if ($isStudentOrParent === false) {
                if ($teacherFilterView->getCurrentTeacher() !== null) {
                    $exams = $examRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $today);
                } else {
                    $exams = $examRepository->findAll($today);
                }
            }

            $exams = array_filter($exams, function (Exam $exam) {
                return $this->isGranted(ExamVoter::Show, $exam);
            });
        }

        $exams = array_filter($exams, function(Exam $exam) {
            return $exam->getDate() !== null;
        });

        $examWeekGroups = $this->grouper->group($exams, ExamWeekStrategy::class);
        $this->sorter->sort($examWeekGroups, ExamWeekGroupStrategy::class);

        $week = $request->query->getInt('week', null);
        $year = $request->query->getInt('year', null);

        $exams = [ ];
        $currentGroup = null;
        $previousGroup = null;
        $nextGroup = null;

        // $week==null
        /** @var ExamWeekGroup $group */
        for($idx = 0; $idx < count($examWeekGroups); $idx++) {
            $group = $examWeekGroups[$idx];

            if($group->getKey()->getWeekNumber() === $week && $group->getKey()->getYear() === $year) {
                $currentGroup = $group;

                $previousGroup = $examWeekGroups[$idx - 1] ?? null;
                $nextGroup = $examWeekGroups[$idx + 1] ?? null;
            }
        }

        if($currentGroup === null && count($examWeekGroups) > 0) {
            $currentGroup = $examWeekGroups[0];

            if(count($examWeekGroups) > 1) {
                $nextGroup = $examWeekGroups[1];
            }
        }

        if($currentGroup !== null) {
            $exams = $currentGroup->getExams();
        }

        $this->sorter->sort($exams, ExamDateSortingStrategy::class);

        return $this->renderWithMessages('exams/index.html.twig', [
            'examWeekGroups' => $examWeekGroups,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'gradeFilter' => $gradeFilterView,
            'studyGroupFilter' => $studyGroupFilterView,
            'showAll' => $all,
            'isVisible' => $isVisible,
            'isVisibleAdmin' => $isVisibleAdmin,
            'exams' => $exams,
            'currentGroup' => $currentGroup,
            'nextGroup' => $nextGroup,
            'previousGroup' => $previousGroup,
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }

    /**
     * @Route("/{uuid}", name="show_exam", requirements={"id": "\d+"})
     */
    public function show(Exam $exam) {
        $this->denyAccessUnlessGranted(ExamVoter::Show, $exam);

        $studyGroups = [ ];
        /** @var Student[] $students */
        $students = $exam->getStudents();

        /** @var StudyGroup[] $studyGroups */
        $studyGroups = $exam->getTuitions()
            ->map(function(Tuition $tuition) {
                return $tuition->getStudyGroup();
            });

        foreach($studyGroups as $studyGroup) {
            foreach($studyGroup->getMemberships() as $membership) {
                $studyGroups[$membership->getStudent()->getId()] = $membership->getStudyGroup();
            }
        }

        $students = $exam->getStudents()->toArray();
        $this->sorter->sort($students, StudentStrategy::class);

        return $this->renderWithMessages('exams/details.html.twig', [
            'exam' => $exam,
            'students' => $students,
            'studyGroups' => $studyGroups,
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }

    /**
     * @Route("/export", name="exams_export")
     */
    public function export(Request $request, DeviceManager $manager) {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new DeviceToken())
            ->setType(DeviceTokenType::Exams())
            ->setUser($user);

        $form = $this->createForm(DeviceTokenTypeForm::class, $deviceToken);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $deviceToken = $manager->persistDeviceToken($deviceToken);
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
}