<?php

namespace App\Controller;

use App\Entity\DeviceToken;
use App\Entity\DeviceTokenType;
use App\Entity\Exam;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Export\ExamIcsExporter;
use App\Form\DeviceTokenType as DeviceTokenTypeForm;
use App\Grouping\ExamDateStrategy;
use App\Grouping\Grouper;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Security\Devices\DeviceManager;
use App\Security\Voter\ExamVoter;
use App\Settings\ExamSettings;
use App\Sorting\ExamDateGroupStrategy;
use App\Sorting\ExamLessonStrategy as ExamDateSortingStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\RefererHelper;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/exams")
 */
class ExamController extends AbstractControllerWithMessages {

    private $grouper;
    private $sorter;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, Grouper $grouper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);

        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("", name="exams")
     */
    public function index(TeacherFilter $teacherFilter, StudentFilter $studentsFilter, GradeFilter $gradeFilter,
                          ExamRepositoryInterface $examRepository, ExamSettings $examSettings,
                          ?int $studentId = null, ?string $teacherAcronym = null, ?int $gradeId = null, ?bool $all = false) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        $studentFilterView = $studentsFilter->handle($studentId, $user);
        $gradeFilterView = $gradeFilter->handle($gradeId, $user);
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $user, $studentFilterView->getCurrentStudent() === null && $gradeFilterView->getCurrentGrade() === null);

        $exams = [ ];
        $today = $all ? null : $this->dateHelper->getToday();

        $isVisible = $examSettings->isVisibileFor($user->getUserType());
        $isVisibleAdmin = false;

        if($isVisible === true || $this->isGranted('ROLE_EXAMS_CREATOR') || $this->isGranted('ROLE_EXAMS_ADMIN')) {
            $isVisible = true;
            $isVisibleAdmin = true;

            if ($studentFilterView->getCurrentStudent() !== null) {
                $exams = $examRepository->findAllByStudents([$studentFilterView->getCurrentStudent()], $today);
            } else {
                if ($gradeFilterView->getCurrentGrade() !== null) {
                    $exams = $examRepository->findAllByGrade($gradeFilterView->getCurrentGrade(), $today);
                } else {
                    if ($isStudentOrParent) {
                        $exams = [];
                    } else {
                        if ($teacherFilterView->getCurrentTeacher() !== null) {
                            $exams = $examRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $today);
                        } else {
                            $exams = $examRepository->findAll($today);
                        }
                    }
                }
            }

            $exams = array_filter($exams, function (Exam $exam) {
                return $this->isGranted(ExamVoter::Show, $exam);
            });
        }

        $examGroups = $this->grouper->group($exams, ExamDateStrategy::class);
        $this->sorter->sort($examGroups, ExamDateGroupStrategy::class);
        $this->sorter->sortGroupItems($examGroups, ExamDateSortingStrategy::class);

        return $this->renderWithMessages('exams/index.html.twig', [
            'examGroups' => $examGroups,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'gradeFilter' => $gradeFilterView,
            'showAll' => $all,
            'isVisible' => $isVisible,
            'isVisibleAdmin' => $isVisibleAdmin
        ]);
    }

    /**
     * @Route("/{id}", name="show_exam", requirements={"id": "\d+"})
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
            'studyGroups' => $studyGroups
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