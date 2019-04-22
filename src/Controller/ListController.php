<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Grouping\Grouper;
use App\Grouping\StudentGradeGroup;
use App\Grouping\StudentGradeStrategy;
use App\Grouping\StudyGroupGradeGroup;
use App\Grouping\StudyGroupGradeStrategy;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\GradeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\GradeStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentGroupMembershipStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\StudyGroupGradeGradeStrategy;
use App\Sorting\StudyGroupStrategy;
use App\Sorting\TeacherStrategy;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractControllerWithMessages {

    private $grouper;
    private $sorter;

    public function __construct(Grouper $grouper, Sorter $sorter,
                                MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper);

        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Lists();
    }

    /**
     * @Route("/lists/tuitions", name="list_tuitions")
     */
    public function tuitions(GradeRepositoryInterface $gradeRepository, StudentRepositoryInterface $studentRepository,
                             TeacherRepositoryInterface $teacherRepository, TuitionRepositoryInterface $tuitionRepository,
                             ?int $studentId = null, ?int $gradeId = null, ?string $teacherAcronym = null) {
        $grades = $gradeRepository->findAll();
        $this->sorter->sort($grades, GradeStrategy::class);

        /** @var StudentGradeGroup[] $studentGroups */
        $studentGroups = $this->grouper->group(
            $studentRepository->findAll(),
            StudentGradeStrategy::class
        );
        $this->sorter->sort($studentGroups, StudentGradeGroupStrategy::class);
        $this->sorter->sortGroupItems($studentGroups, StudentStrategy::class);

        $teachers = $teacherRepository->findAll();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $student = $studentId !== null ? $studentRepository->findOneById($studentId) : null;
        $grade = $gradeId !== null ? $gradeRepository->findOneById($gradeId) : null;
        $teacher = $teacherAcronym !== null ? $teacherRepository->findOneByAcronym($teacherAcronym) : null;

        $tuitions = [ ];
        $memberships = [ ];

        if($student !== null) {
            $tuitions = $tuitionRepository->findAllByStudents([$student]);

            foreach($tuitions as $tuition) {
                /** @var StudyGroupMembership|null $membership */
                $membership = $tuition->getStudyGroup()->getMemberships()->filter(function(StudyGroupMembership $membership) use ($student) {
                    return $membership->getStudent()->getId() === $student->getId();
                })->first();

                $memberships[$tuition->getExternalId()] = $membership->getType();
            }
        } else if($grade !== null) {
            $tuitions = $tuitionRepository->findAllByGrades([$grade]);
        } else if($teacher !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacher);
        }

        return $this->render('lists/tuitions.html.twig', [
            'grades' => $grades,
            'studentGroups' => $studentGroups,
            'teachers' => $teachers,
            'student' => $student,
            'grade' => $grade,
            'teacher' => $teacher,
            'tuitions' => $tuitions,
            'memberships' => $memberships
        ]);
    }

    /**
     * @Route("/lists/tuitions/{id}", name="list_tuition")
     */
    public function tuition(Tuition $tuition, TuitionRepositoryInterface $tuitionRepository, ExamRepositoryInterface $examRepository) {
        $tuition = $tuitionRepository->findOneById($tuition->getId());
        $memberships = $tuition->getStudyGroup()->getMemberships()->toArray();
        $this->sorter->sort($memberships, StudentGroupMembershipStrategy::class);

        $exams = $examRepository->findAllByTuitions([$tuition]);

        return $this->render('lists/tuition.html.twig', [
            'tuition' => $tuition,
            'memberships' => $memberships,
            'exams' => $exams
        ]);
    }

    /**
     * @Route("/lists/study_groups", name="list_studygroups")
     */
    public function studyGroups(StudyGroupRepositoryInterface $studyGroupRepository, ?int $studyGroupId = null) {
        $studyGroups = $studyGroupRepository->findAll();
        /** @var StudyGroupGradeGroup[] $studyGroupGroups */
        $studyGroupGroups = $this->grouper->group($studyGroups, StudyGroupGradeStrategy::class);
        $this->sorter->sort($studyGroupGroups, StudyGroupGradeGradeStrategy::class);
        $this->sorter->sortGroupItems($studyGroupGroups, StudyGroupStrategy::class);

        $students = [ ];

        $studyGroup = $studyGroupId !== null ? $studyGroupRepository->findOneById($studyGroupId) : null;

        if($studyGroup !== null) {
            $students = $studyGroup->getMemberships()->map(function(StudyGroupMembership $membership) {
                return $membership->getStudent();
            })->toArray();
        }

        $this->sorter->sort($students, StudentStrategy::class);

        return $this->render('lists/study_groups.html.twig', [
            'studyGroups' => $studyGroupGroups,
            'studyGroup' => $studyGroup,
            'students' => $students,
        ]);
    }

}