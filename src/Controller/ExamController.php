<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\ExamDateStrategy;
use App\Grouping\Grouper;
use App\Grouping\StudentGradeStrategy;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Sorting\ExamDateGroupStrategy;
use App\Sorting\ExamLessonStrategy as ExamDateSortingStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\TeacherStrategy;
use App\Utils\ArrayUtils;
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
    public function index(ExamRepositoryInterface $examRepository, StudentRepositoryInterface $studentRepository,
                          TeacherRepositoryInterface $teacherRepository, DateHelper $dateHelper,
                          ?int $studentId = null, ?string $teacherAcronym = null, ?bool $all = false) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        if($isStudentOrParent) {
            $students = $user->getStudents()->toArray();
            $teachers = [ ];
        } else {
            $students = $studentRepository->findAll();
            $teachers = $teacherRepository->findAll();
        }

        $students = ArrayUtils::createArrayWithKeys($students, function(Student $student) { return $student->getId();});
        $teachers = ArrayUtils::createArrayWithKeys($teachers, function(Teacher $teacher) { return $teacher->getAcronym();});

        $student = $studentId !== null ?
            $students[$studentId] ?? null : null;
        $teacher = $teacherAcronym !== null ?
            $teachers[$teacherAcronym] ?? $user->getTeacher() : $user->getTeacher();

        $studentGroups = $this->grouper->group($students, StudentGradeStrategy::class);
        $this->sorter->sort($studentGroups, StudentGradeGroupStrategy::class);
        $this->sorter->sortGroupItems($studentGroups, StudentStrategy::class);

        $this->sorter->sort($teachers, TeacherStrategy::class);

        $exams = [ ];
        $today = $all ? null : $this->dateHelper->getToday();

        if($student !== null) {
            $exams = $examRepository->findAllByStudents([$student], $today);
        } else if($isStudentOrParent) {
            $exams = $examRepository->findAllByStudents($user->getStudents()->toArray(), $today);
        } else if($teacher !== null) {
            $exams = $examRepository->findAllByTeacher($teacher, $today);
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
            'studentGroups' => $studentGroups,
            'teachers' => $teachers,
            'teacher' => $teacher,
            'student' => $student,
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