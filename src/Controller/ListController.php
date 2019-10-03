<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\StudyGroupType;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Entity\User;
use App\Grouping\Grouper;
use App\Grouping\StudentGradeGroup;
use App\Grouping\StudentGradeStrategy;
use App\Grouping\StudyGroupGradeGroup;
use App\Grouping\StudyGroupGradeStrategy;
use App\Grouping\TeacherFirstCharacterStrategy;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\GradeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentGroupMembershipStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\StudyGroupGradeGroupStrategy;
use App\Sorting\StudyGroupStrategy;
use App\Sorting\SubjectNameStrategy;
use App\Sorting\TeacherFirstCharacterGroupStrategy;
use App\Sorting\TeacherStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\SubjectFilter;
use App\View\Filter\TeacherFilter;
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
    public function tuitions(GradeFilter $gradeFilter, StudentFilter $studentFilter, TeacherFilter $teacherFilter, TuitionRepositoryInterface $tuitionRepository,
                             ?int $studentId = null, ?int $gradeId = null, ?string $teacherAcronym = null) {
        /** @var User $user */
        $user = $this->getUser();

        $gradeFilterView = $gradeFilter->handle($gradeId, $user);
        $studentFilterView = $studentFilter->handle($studentId, $user);
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $user);

        $tuitions = [ ];
        $memberships = [ ];

        if($studentFilterView->getCurrentStudent() !== null) {
            $tuitions = $tuitionRepository->findAllByStudents([$studentFilterView->getCurrentStudent()]);

            foreach($tuitions as $tuition) {
                /** @var StudyGroupMembership|null $membership */
                $membership = $tuition->getStudyGroup()->getMemberships()->filter(function(StudyGroupMembership $membership) use ($studentFilterView) {
                    return $membership->getStudent()->getId() === $studentFilterView->getCurrentStudent()->getId();
                })->first();

                $memberships[$tuition->getExternalId()] = $membership->getType();
            }
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $tuitions = $tuitionRepository->findAllByGrades([$gradeFilterView->getCurrentGrade()]);
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher());
        }

        return $this->render('lists/tuitions.html.twig', [
            'gradeFilter' => $gradeFilterView,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
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
    public function studyGroups(StudyGroupFilter $studyGroupFilter, ?int $studyGroupId = null) {
        /** @var User $user */
        $user = $this->getUser();

        $studyGroupFilterView = $studyGroupFilter->handle($studyGroupId, $user);

        $students = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            $students = $studyGroupFilterView->getCurrentStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                return $membership->getStudent();
            })->toArray();
        }

        $this->sorter->sort($students, StudentStrategy::class);

        $grade = null;
        $gradeTeachers = [ ];
        $substitutionalGradeTeachers = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null && $studyGroupFilterView->getCurrentStudyGroup()->getType()->equals(StudyGroupType::Grade())) {
            /** @var Grade $grade */
            $grade = $studyGroupFilterView->getCurrentStudyGroup()->getGrades()->first();
            $gradeTeachers = array_map(function(GradeTeacher $gradeTeacher) {
                return $gradeTeacher->getTeacher();
            }, array_filter($grade->getTeachers()->toArray(), function(GradeTeacher $gradeTeacher){
                    return $gradeTeacher->getType()->equals(GradeTeacherType::Primary());
                })
            );

            $substitutionalGradeTeachers = array_map(function(GradeTeacher $gradeTeacher) {
                return $gradeTeacher->getTeacher();
            }, array_filter($grade->getTeachers()->toArray(), function(GradeTeacher $gradeTeacher){
                    return $gradeTeacher->getType()->equals(GradeTeacherType::Substitutional());
                })
            );
        }

        return $this->render('lists/study_groups.html.twig', [
            'studyGroupFilter' => $studyGroupFilterView,
            'students' => $students,
            'grade' => $grade,
            'gradeTeachers' => $gradeTeachers,
            'substitutionalGradeTeachers' => $substitutionalGradeTeachers
        ]);
    }

    /**
     * @Route("/lists/teachers", name="list_teachers")
     */
    public function teachers(SubjectFilter $subjectFilter, TeacherRepositoryInterface $teacherRepository, ?string $subject) {
        $subjectFilterView = $subjectFilter->handle($subject);
        $teachers = [ ];

        if($subjectFilterView->getCurrentSubject() !== null) {
            $teachers = $teacherRepository->findAllBySubject($subjectFilterView->getCurrentSubject());
        } else {
            $teachers = $teacherRepository->findAll();
        }

        $groups = $this->grouper->group($teachers, TeacherFirstCharacterStrategy::class);
        $this->sorter->sort($groups, TeacherFirstCharacterGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, TeacherStrategy::class);

        return $this->render('lists/teachers.html.twig', [
            'groups' => $groups,
            'subjectFilter' => $subjectFilterView
        ]);
    }
}