<?php

namespace App\Controller;

use App\Entity\LearningManagementSystem;
use App\Export\LearningManagementSystemInfoCsvExporter;
use App\Repository\StudyGroupMembershipRepositoryInterface;
use App\View\Filter\LearningManagementSystemFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
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
use App\Entity\TeacherTag;
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
use App\Section\SectionResolverInterface;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\ListsVoter;
use App\Sorting\Sorter;
use App\Sorting\StudentGroupMembershipStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\StudyGroupStrategy;
use App\Sorting\TeacherFirstCharacterGroupStrategy;
use App\Sorting\TeacherStrategy;
use App\Sorting\TuitionStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\SubjectFilter;
use App\View\Filter\TeacherFilter;
use App\View\Filter\TeacherTagFilter;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractControllerWithMessages {

    public function __construct(private Grouper $grouper, private Sorter $sorter, private ImportDateTypeRepositoryInterface $importDateTimeRepository,
                                MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Lists;
    }

    #[Route(path: '/tuitions', name: 'list_tuitions')]
    public function tuitions(SectionFilter $sectionFilter, GradeFilter $gradeFilter, StudentFilter $studentFilter, TeacherFilter $teacherFilter, TuitionRepositoryInterface $tuitionRepository, Request $request): Response {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $studentFilterView->getCurrentStudent() === null);

        $tuitions = [ ];
        $memberships = [ ];
        $teacherMailAddresses = [ ];

        if($studentFilterView->getCurrentStudent() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllByStudents([$studentFilterView->getCurrentStudent()], $sectionFilterView->getCurrentSection());

            foreach($tuitions as $tuition) {
                /** @var StudyGroupMembership|null $membership */
                $membership = $tuition->getStudyGroup()->getMemberships()->filter(fn(StudyGroupMembership $membership) => $membership->getStudent()->getId() === $studentFilterView->getCurrentStudent()->getId())->first();

                $memberships[$tuition->getExternalId()] = $membership->getType();
            }
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $tuitions = $tuitionRepository->findAllByGrades([$gradeFilterView->getCurrentGrade()], $sectionFilterView->getCurrentSection());
        } else if($teacherFilterView->getCurrentTeacher() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());
        }

        if($studentFilterView->getCurrentStudent() !== null || $gradeFilterView->getCurrentGrade() !== null) {
            foreach($tuitions as $tuition) {
                foreach($tuition->getTeachers() as $teacher) {
                    if($teacher->getEmail() !== null) {
                        $teacherMailAddresses[] = $teacher->getEmail();
                    }
                }
            }

            $teacherMailAddresses = array_unique($teacherMailAddresses);
        }

        $tuitions = array_filter($tuitions, fn(Tuition $tuition) => $tuition->getSection() === $sectionFilterView->getCurrentSection());

        $this->sorter->sort($tuitions, TuitionStrategy::class);

        return $this->renderWithMessages('lists/tuitions.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'tuitions' => $tuitions,
            'memberships' => $memberships,
            'teacherMailAddresses' => $teacherMailAddresses,
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Tuition::class)
        ]);
    }

    #[Route(path: '/tuitions/{uuid}', name: 'list_tuition')]
    public function tuition(Tuition $tuition, TuitionRepositoryInterface $tuitionRepository, ExamRepositoryInterface $examRepository): Response {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        $tuition = $tuitionRepository->findOneById($tuition->getId());
        /** @var StudyGroupMembership[] $memberships */
        $memberships = $tuition->getStudyGroup()->getMemberships()->toArray();
        $this->sorter->sort($memberships, StudentGroupMembershipStrategy::class);

        $exams = $examRepository->findAllByTuitions([$tuition], null, true);

        $exams = array_filter($exams, fn(Exam $exam) => $this->isGranted(ExamVoter::Show, $exam));

        $types = [ ];

        foreach($memberships as $membership) {
            if(!array_key_exists($membership->getType(), $types)) {
                $types[$membership->getType()] = 0;
            }

            $types[$membership->getType()]++;
        }

        return $this->renderWithMessages('lists/tuition.html.twig', [
            'tuition' => $tuition,
            'memberships' => $memberships,
            'exams' => $exams,
            'today' => $this->dateHelper->getToday(),
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Tuition::class),
            'types' => $types
        ]);
    }

    #[Route(path: '/tuitions/{uuid}/export', name: 'export_tuition')]
    public function exportTuition(Tuition $tuition, TuitionCsvExporter $tuitionCsvExporter): Response {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        return $tuitionCsvExporter->getCsvResponse($tuition);
    }

    #[Route(path: '/study_groups', name: 'list_studygroups')]
    public function studyGroups(SectionFilter $sectionFilter, StudyGroupFilter $studyGroupFilter, StudentFilter $studentFilter,
                                TuitionRepositoryInterface $tuitionRepository, Request $request, Sorter $sorter): Response {
        $this->denyAccessUnlessGranted(ListsVoter::StudyGroups);

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group', null), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);

        $students = [ ];
        $studyGroups = [ ];
        $memberships = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            /** @var StudyGroupMembership $membership */
            foreach($studyGroupFilterView->getCurrentStudyGroup()->getMemberships() as $membership) {
                $students[] = $membership->getStudent();
                $memberships[$membership->getStudent()->getId()] = $membership->getType();
            }

            $this->sorter->sort($students, StudentStrategy::class);
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $studyGroups = [ ];
            $memberships = [ ];

            /** @var StudyGroupMembership $membership */
            foreach($studentFilterView->getCurrentStudent()->getStudyGroupMemberships() as $membership) {
                $studyGroups[] = $membership->getStudyGroup();
                $memberships[$membership->getStudyGroup()->getId()] = $membership->getType();
            }

            $this->sorter->sort($studyGroups, StudyGroupStrategy::class);
        }

        $studyGroups = array_filter($studyGroups, fn(StudyGroup $studyGroup) => $studyGroup->getSection() === $sectionFilterView->getCurrentSection());

        $grade = null;
        $gradeTeachers = [ ];
        $substitutionalGradeTeachers = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null && $studyGroupFilterView->getCurrentStudyGroup()->getType() === StudyGroupType::Grade) {
            /** @var Grade $grade */
            $grade = $studyGroupFilterView->getCurrentStudyGroup()->getGrades()->first();
        } else if($studentFilterView->getCurrentStudent() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $grade = $studentFilterView->getCurrentStudent()->getGrade($sectionFilterView->getCurrentSection());
        }

        if($grade !== null) {
            $gradeTeachers = array_map(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getTeacher(), array_filter($grade->getTeachers()->toArray(), fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getType() === GradeTeacherType::Primary));

            $substitutionalGradeTeachers = array_map(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getTeacher(), array_filter($grade->getTeachers()->toArray(), fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getType() === GradeTeacherType::Substitute));
        }

        $tuitions = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            if($studyGroupFilterView->getCurrentStudyGroup()->getType() === StudyGroupType::Grade) {
                $tuitions = $tuitionRepository->findAllByGrades($studyGroupFilterView->getCurrentStudyGroup()->getGrades()->toArray(), $sectionFilterView->getCurrentSection());
            } else {
                $tuitions = $studyGroupFilterView->getCurrentStudyGroup()->getTuitions()->toArray();
            }
        }

        $tuitions = array_filter($tuitions, fn(Tuition $tuition) => $tuition->getSection() === $sectionFilterView->getCurrentSection());

        $sorter->sort($tuitions, TuitionStrategy::class);

        return $this->renderWithMessages('lists/study_groups.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'studyGroupFilter' => $studyGroupFilterView,
            'studentFilter' => $studentFilterView,
            'study_groups' => $studyGroups,
            'memberships' => $memberships,
            'students' => $students,
            'grade' => $grade,
            'gradeTeachers' => $gradeTeachers,
            'substitutionalGradeTeachers' => $substitutionalGradeTeachers,
            'tuitions' => $tuitions,
            'today' => $this->dateHelper->getToday(),
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(StudyGroup::class)
        ]);
    }

    #[Route(path: '/study_groups/{uuid}/export', name: 'export_studygroup')]
    public function exportStudyGroup(StudyGroup $studyGroup, StudyGroupCsvExporter $csvExporter): Response {
        $this->denyAccessUnlessGranted(ListsVoter::StudyGroups);

        return $csvExporter->getCsvResponse($studyGroup);
    }

    #[Route(path: '/teachers', name: 'list_teachers')]
    public function teachers(SubjectFilter $subjectFilter, TeacherTagFilter $tagFilter, TeacherRepositoryInterface $teacherRepository,
                             SectionResolverInterface $sectionResolver, Request $request): Response {
        $this->denyAccessUnlessGranted(ListsVoter::Teachers);

        $subjectFilterView = $subjectFilter->handle($request->query->get('subject', null));
        $tagFilterView = $tagFilter->handle($request->query->get('tag', null));

        $teachers = $teacherRepository->findAllBySubjectAndTag($subjectFilterView->getCurrentSubject(), $tagFilterView->getCurrentTag());

        if($tagFilterView->getCurrentTag() !== null && $tagFilterView->getCurrentTag()->getId() === null) {
            $teachers = $this->filterImplicitTeacherTag($teachers, $tagFilterView->getCurrentTag());
        }

        $groups = $this->grouper->group($teachers, TeacherFirstCharacterStrategy::class);
        $this->sorter->sort($groups, TeacherFirstCharacterGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, TeacherStrategy::class);

        return $this->renderWithMessages('lists/teachers.html.twig', [
            'groups' => $groups,
            'subjectFilter' => $subjectFilterView,
            'tagFilter' => $tagFilterView,
            'section' => $sectionResolver->getCurrentSection(),
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Teacher::class)
        ]);
    }

    /**
     * @param Teacher[] $teachers
     * @return Teacher[]
     */
    private function filterImplicitTeacherTag(array $teachers, TeacherTag $tag): array {
        return array_filter($teachers, function(Teacher $teacher) use ($tag) {
            /** @var GradeTeacher $gradeTeacher */
            foreach($teacher->getGrades() as $gradeTeacher) {
                if($gradeTeacher->getType() === GradeTeacherType::Primary && $tag->getUuid()->toString() === TeacherTag::GradeTeacherTagUuid) {
                    return true;
                }

                if($gradeTeacher->getType() === GradeTeacherType::Substitute && $tag->getUuid()->toString() === TeacherTag::SubstituteGradeTeacherTagUuid) {
                    return true;
                }
            }

            return false;
        });
    }

    #[Route(path: '/privacy', name: 'list_privacy')]
    public function privacy(SectionResolverInterface $sectionResolver, StudyGroupFilter $studyGroupFilter, Request $request, StudentRepositoryInterface $studentRepository, PrivacyCategoryRepositoryInterface $privacyCategoryRepository): Response {
        $this->denyAccessUnlessGranted(ListsVoter::Privacy);

        /** @var User $user */
        $user = $this->getUser();

        $q = $request->query->get('q', null);
        $studygroupView = $studyGroupFilter->handle($request->query->get('study_group', null), $sectionResolver->getCurrentSection(), $user);

        // Filter categories
        $categories = $privacyCategoryRepository->findAll();
        $filteredCategories = [ ];

        foreach($categories as $category) {
            if($request->query->get($category->getUuid()->toString()) === '✓') {
                $filteredCategories[] = $category->getUuid()->toString();
            }
        }

        $students = [ ];

        if($q !== null) {
            $students = $studentRepository->findAllByQuery($q);
        } else if($studygroupView->getCurrentStudyGroup() !== null) {
            $students = $studentRepository->findAllByStudyGroups([$studygroupView->getCurrentStudyGroup()]);
        }

        $this->sorter->sort($students, StudentStrategy::class);

        return $this->render('lists/privacy.html.twig', [
            'students' => $students,
            'categories' => $categories,
            'filteredCategories' => $filteredCategories,
            'q' => $q,
            'section' => $sectionResolver->getCurrentSection(),
            'studyGroupFilter' => $studygroupView,
            'isStart' => $request->query->has('q') === false && $request->query->has('study_group') === false,
            'last_import_categories' => $this->importDateTimeRepository->findOneByEntityClass(PrivacyCategory::class),
            'last_import_students' => $this->importDateTimeRepository->findOneByEntityClass(Student::class)
        ]);
    }

    #[Route('/lms', name: 'list_lms')]
    public function lms(Request $request, LearningManagementSystemFilter $lmsFilter, SectionResolverInterface $sectionResolver, StudyGroupFilter $studyGroupFilter, StudentRepositoryInterface $studentRepository) {
        $this->denyAccessUnlessGranted(ListsVoter::LearningManagementSystems);

        /** @var User $user */
        $user = $this->getUser();

        $lmsFilterView = $lmsFilter->handle($request->query->get('lms'));
        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group', null), $sectionResolver->getCurrentSection(), $user);

        $students = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            $students = $studentRepository->findAllByStudyGroups([$studyGroupFilterView->getCurrentStudyGroup()]);
        }

        $this->sorter->sort($students, StudentStrategy::class);

        return $this->render('lists/lms.html.twig', [
            'students' => $students,
            'lmsFilter' => $lmsFilterView,
            'studyGroupFilter' => $studyGroupFilterView,
            'last_import_lms' => $this->importDateTimeRepository->findOneByEntityClass(LearningManagementSystem::class),
            'last_import_students' => $this->importDateTimeRepository->findOneByEntityClass(Student::class)
        ]);
    }

    #[Route('/lms/{lms}/{studyGroup}/export', name: 'export_lms')]
    #[ParamConverter('lms', options: ['mapping' => ['lms' => 'uuid']])]
    #[ParamConverter('studyGroup', options: ['mapping' => ['studyGroup' => 'uuid']])]
    public function exportLms(LearningManagementSystem $lms, StudyGroup $studyGroup, LearningManagementSystemInfoCsvExporter $exporter): Response {
        return $exporter->getCsvResponse($lms, $studyGroup);
    }
}