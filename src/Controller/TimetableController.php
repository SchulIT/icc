<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Grouping\Grouper;
use App\Grouping\StudentGradeStrategy;
use App\Message\DismissedMessagesHelper;
use App\Repository\MessageRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\TeacherStrategy;
use App\Timetable\TimetableHelper;
use App\Utils\ArrayUtils;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;

class TimetableController extends AbstractControllerWithMessages {

    private $timetableHelper;
    private $grouper;
    private $sorter;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, TimetableHelper $timetableHelper, Grouper $grouper, Sorter $sorter) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper);

        $this->timetableHelper = $timetableHelper;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("/timetable", name="timetable")
     */
    public function index(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository,
                          TimetableWeekRepositoryInterface $weekRepository, TimetableLessonRepositoryInterface $lessonRepository,
                          TimetablePeriodRepositoryInterface $periodRepository, ?int $studentId = null, ?string $teacherAcronym = null,
                          ?string $roomId = null) {
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

        $weeks = $weekRepository->findAll();
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Timetable();
    }
}