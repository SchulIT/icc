<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Tuition;
use App\Entity\UserType;
use App\Grouping\ExamDateStrategy;
use App\Grouping\Grouper;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Sorting\ExamDateGroupStrategy;
use App\Sorting\ExamLessonStrategy as ExamDateSortingStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;

class ExamController extends AbstractControllerWithMessages {

    private $grouper;
    private $sorter;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, Grouper $grouper, Sorter $sorter) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper);

        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("/exams", name="exams")
     */
    public function index(TeacherFilter $teacherFilter, StudentFilter $studentsFilter,
                          ExamRepositoryInterface $examRepository,
                          ?int $studentId = null, ?string $teacherAcronym = null, ?bool $all = false) {
        $user = $this->getUser();
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        $studentFilterView = $studentsFilter->handle($studentId, $this->getUser());
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $this->getUser());

        $exams = [ ];
        $today = $all ? null : $this->dateHelper->getToday();

        if($studentFilterView->getCurrentStudent() !== null) {
            $exams = $examRepository->findAllByStudents([$studentFilterView->getCurrentStudent()], $today);
        } else if($isStudentOrParent) {
            $exams = [ ];
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $exams = $examRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $today);
        } else {
            $exams = $examRepository->findAll($today);
        }

        $exams = array_filter($exams, function(Exam $exam) {
            return $this->isGranted(ExamVoter::SHOW, $exam);
        });

        $examGroups = $this->grouper->group($exams, ExamDateStrategy::class);
        $this->sorter->sort($examGroups, ExamDateGroupStrategy::class);
        $this->sorter->sortGroupItems($examGroups, ExamDateSortingStrategy::class);

        return $this->render('exams/index.html.twig', [
            'examGroups' => $examGroups,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'showAll' => $all
        ]);
    }

    /**
     * @Route("/exams/{id}", name="show_exam")
     */
    public function show(Exam $exam) {
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

        return $this->render('exams/details.html.twig', [
            'exam' => $exam,
            'students' => $students,
            'studyGroups' => $studyGroups
        ]);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Exams();
    }
}