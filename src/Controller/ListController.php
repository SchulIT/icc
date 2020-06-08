<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Entity\MessageScope;
use App\Entity\PrivacyCategory;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\StudyGroupType;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Entity\User;
use App\Export\StudyGroupCsvExporter;
use App\Export\TuitionCsvExporter;
use App\Grouping\Grouper;
use App\Grouping\TeacherFirstCharacterStrategy;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\PrivacyCategoryRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\ListsVoter;
use App\Sorting\Sorter;
use App\Sorting\StudentGroupMembershipStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\TeacherFirstCharacterGroupStrategy;
use App\Sorting\TeacherStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\SubjectFilter;
use App\View\Filter\TeacherFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use SchoolIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractControllerWithMessages {

    private $grouper;
    private $sorter;
    private $importDateTimeRepository;

    public function __construct(Grouper $grouper, Sorter $sorter, ImportDateTypeRepositoryInterface $importDateTimeRepository,
                                MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);

        $this->grouper = $grouper;
        $this->sorter = $sorter;
        $this->importDateTimeRepository = $importDateTimeRepository;
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Lists();
    }

    /**
     * @Route("/lists/tuitions", name="list_tuitions")
     */
    public function tuitions(GradeFilter $gradeFilter, StudentFilter $studentFilter, TeacherFilter $teacherFilter, TuitionRepositoryInterface $tuitionRepository, Request $request) {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        /** @var User $user */
        $user = $this->getUser();

        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, $gradeFilterView->getCurrentGrade() === null && $studentFilterView->getCurrentStudent() === null);

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

        return $this->renderWithMessages('lists/tuitions.html.twig', [
            'gradeFilter' => $gradeFilterView,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'tuitions' => $tuitions,
            'memberships' => $memberships,
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Tuition::class)
        ]);
    }

    /**
     * @Route("/lists/tuitions/{uuid}", name="list_tuition")
     */
    public function tuition(Tuition $tuition, TuitionRepositoryInterface $tuitionRepository, ExamRepositoryInterface $examRepository) {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        $tuition = $tuitionRepository->findOneById($tuition->getId());
        $memberships = $tuition->getStudyGroup()->getMemberships()->toArray();
        $this->sorter->sort($memberships, StudentGroupMembershipStrategy::class);

        $exams = $examRepository->findAllByTuitions([$tuition]);

        $exams = array_filter($exams, function(Exam $exam) {
            return $this->isGranted(ExamVoter::Show, $exam);
        });

        return $this->renderWithMessages('lists/tuition.html.twig', [
            'tuition' => $tuition,
            'memberships' => $memberships,
            'exams' => $exams,
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Tuition::class)
        ]);
    }

    /**
     * @Route("/lists/tuitions/{uuid}/export", name="export_tuition")
     */
    public function exportTuition(Tuition $tuition, TuitionCsvExporter $tuitionCsvExporter) {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        return $tuitionCsvExporter->getCsvResponse($tuition);
    }

    /**
     * @Route("/lists/study_groups", name="list_studygroups")
     */
    public function studyGroups(StudyGroupFilter $studyGroupFilter, Request $request) {
        $this->denyAccessUnlessGranted(ListsVoter::StudyGroups);

        /** @var User $user */
        $user = $this->getUser();

        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group', null), $user);

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
                    return $gradeTeacher->getType()->equals(GradeTeacherType::Substitute());
                })
            );
        }

        return $this->renderWithMessages('lists/study_groups.html.twig', [
            'studyGroupFilter' => $studyGroupFilterView,
            'students' => $students,
            'grade' => $grade,
            'gradeTeachers' => $gradeTeachers,
            'substitutionalGradeTeachers' => $substitutionalGradeTeachers,
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(StudyGroup::class)
        ]);
    }

    /**
     * @Route("/lists/study_groups/{uuid}/export", name="export_studygroup")
     */
    public function exportStudyGroup(StudyGroup $studyGroup, StudyGroupCsvExporter $csvExporter) {
        $this->denyAccessUnlessGranted(ListsVoter::StudyGroups);

        return $csvExporter->getCsvResponse($studyGroup);
    }

    /**
     * @Route("/lists/teachers", name="list_teachers")
     */
    public function teachers(SubjectFilter $subjectFilter, TeacherRepositoryInterface $teacherRepository, Request $request) {
        $this->denyAccessUnlessGranted(ListsVoter::Teachers);

        $subjectFilterView = $subjectFilter->handle($request->query->get('subject', null));
        $teachers = [ ];

        if($subjectFilterView->getCurrentSubject() !== null) {
            $teachers = $teacherRepository->findAllBySubject($subjectFilterView->getCurrentSubject());
        } else {
            $teachers = $teacherRepository->findAll();
        }

        $groups = $this->grouper->group($teachers, TeacherFirstCharacterStrategy::class);
        $this->sorter->sort($groups, TeacherFirstCharacterGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, TeacherStrategy::class);

        return $this->renderWithMessages('lists/teachers.html.twig', [
            'groups' => $groups,
            'subjectFilter' => $subjectFilterView,
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Teacher::class)
        ]);
    }

    /**
     * @Route("/lists/privacy", name="list_privacy")
     */
    public function privacy(StudyGroupFilter $studyGroupFilter, Request $request, StudentRepositoryInterface $studentRepository, PrivacyCategoryRepositoryInterface $privacyCategoryRepository) {
        $this->denyAccessUnlessGranted(ListsVoter::Privacy);

        /** @var User $user */
        $user = $this->getUser();

        $q = $request->query->get('q', null);
        $studygroupView = $studyGroupFilter->handle($request->query->get('study_group', null), $user);

        $students = [ ];

        if($q !== null) {
            $students = $studentRepository->findAllByQuery($q);
        } else if($studygroupView->getCurrentStudyGroup() !== null) {
            $students = $studentRepository->findAllByStudyGroups([$studygroupView->getCurrentStudyGroup()]);
        }

        $this->sorter->sort($students, StudentStrategy::class);

        return $this->render('lists/privacy.html.twig', [
            'students' => $students,
            'categories' => $privacyCategoryRepository->findAll(),
            'q' => $q,
            'studyGroupFilter' => $studygroupView,
            'isStart' => $request->query->has('q') === false && $request->query->has('study_group') === false,
            'last_import_categories' => $this->importDateTimeRepository->findOneByEntityClass(PrivacyCategory::class),
            'last_import_students' => $this->importDateTimeRepository->findOneByEntityClass(Student::class)
        ]);
    }
}