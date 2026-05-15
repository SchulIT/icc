<?php

namespace App\Common\Controller;

use App\Common\Entity\Grade;
use App\Common\Entity\GradeTeacher;
use App\Common\Entity\GradeTeacherType;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\StudyGroupType;
use App\Common\Entity\Teacher;
use App\Common\Entity\TeacherTag;
use App\Common\Entity\Tuition;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Repository\TeacherRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Repository\UserRepositoryInterface;
use App\Framework\Controller\AbstractControllerWithMessages;
use App\Exam\Entity\Exam;
use App\Exam\Repository\ExamRepositoryInterface;
use App\LearningManagementSystem\Export\LearningManagementSystemInfoCsvExporter;
use App\Common\Export\StudyGroupCsvExporter;
use App\Common\Export\TuitionCsvExporter;
use App\Framework\Grouping\Grouper;
use App\Common\Grouping\TeacherFirstCharacterStrategy;
use App\LearningManagementSystem\Entity\LearningManagementSystem;
use App\Message\DismissedMessagesHelper;
use App\Message\Entity\MessageScope;
use App\Message\Repository\MessageRepositoryInterface;
use App\Privacy\Entity\PrivacyCategory;
use App\Privacy\Repository\PrivacyCategoryRepositoryInterface;
use App\Framework\Import\Repository\ImportDateTypeRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Exam\Voter\ExamVoter;
use App\Common\Voter\ListsVoter;
use App\Chat\Settings\ChatSettings;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudentGroupMembershipStrategy;
use App\Common\Sorting\StudentStrategy;
use App\Common\Sorting\StudyGroupStrategy;
use App\Common\Sorting\TeacherFirstCharacterGroupStrategy;
use App\Common\Sorting\TeacherStrategy;
use App\Common\Sorting\TuitionStrategy;
use App\Common\View\Filter\GradeFilter;
use App\LearningManagementSystem\View\Filter\LearningManagementSystemFilter;
use App\Common\View\Filter\SectionFilter;
use App\Common\View\Filter\StudentFilter;
use App\Common\View\Filter\StudyGroupFilter;
use App\Common\View\Filter\SubjectFilter;
use App\Common\View\Filter\TeacherFilter;
use App\Common\View\Filter\TeacherTagFilter;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function tuitions(SectionFilter $sectionFilter, GradeFilter $gradeFilter, StudentFilter $studentFilter, TeacherFilter $teacherFilter, SubjectFilter $subjectFilter,
                             TuitionRepositoryInterface $tuitionRepository, ChatSettings $chatSettings, UserRepositoryInterface $userRepository, Request $request): Response {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $subjectFilterView = $subjectFilter->handle($request->query->get('subject', null), false);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $gradeFilterView->getCurrentGrade() === null && $studentFilterView->getCurrentStudent() === null && $subjectFilterView->getCurrentSubject() === null);

        $tuitions = [ ];
        $memberships = [ ];
        $teacherMailAddresses = [ ];
        $teacherPrivateMessageLink = null;

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
        } else if($subjectFilterView->getCurrentSubject() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllBySubjects([$subjectFilterView->getCurrentSubject()], $sectionFilterView->getCurrentSection());
        }

        if($studentFilterView->getCurrentStudent() !== null || $gradeFilterView->getCurrentGrade() !== null) {
            $teachers = [ ];

            foreach($tuitions as $tuition) {
                foreach($tuition->getTeachers() as $teacher) {
                    $teachers[] = $teacher;

                    if($teacher->getEmail() !== null) {
                        $teacherMailAddresses[] = $teacher->getEmail();
                    }
                }
            }

            $teacherMailAddresses = array_unique($teacherMailAddresses);

            if(in_array(UserType::Teacher, $chatSettings->getAllowedRecipients($user->getUserType())) && in_array($user->getUserType(), $chatSettings->getEnabledUserTypes())) {
                $users = $userRepository->findAllTeachers($teachers);
                $uuids = array_map(fn(User $user) => $user->getUuid(), $users);

                $teacherPrivateMessageLink = $this->generateUrl('new_chat', [
                    'recipients' => $uuids,
                ]);
            }
        }

        $tuitions = array_filter($tuitions, fn(Tuition $tuition) => $tuition->getSection() === $sectionFilterView->getCurrentSection());

        $this->sorter->sort($tuitions, TuitionStrategy::class);

        return $this->renderWithMessages('lists/tuitions.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'subjectFilter' => $subjectFilterView,
            'tuitions' => $tuitions,
            'memberships' => $memberships,
            'teacherMailAddresses' => $teacherMailAddresses,
            'teacherPrivateMessageLink' => $teacherPrivateMessageLink,
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Tuition::class)
        ]);
    }

    #[Route(path: '/tuitions/{uuid}', name: 'list_tuition')]
    public function tuition(#[MapEntity(mapping: ['uuid' => 'uuid'])] Tuition $tuition, TuitionRepositoryInterface $tuitionRepository, ExamRepositoryInterface $examRepository): Response {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        $tuition = $tuitionRepository->findOneById($tuition->getId());
        /** @var StudyGroupMembership[] $memberships */
        $memberships = $tuition->getStudyGroup()->getMemberships()->toArray();
        $this->sorter->sort($memberships, StudentGroupMembershipStrategy::class);

        $exams = $examRepository->findAllByTuitions([$tuition], null, true);

        $exams = array_filter($exams, fn(Exam $exam) => $this->isGranted(ExamVoter::Show, $exam));

        $types = [ ];
        $genders = [ ];

        foreach($memberships as $membership) {
            if(!array_key_exists($membership->getType(), $types)) {
                $types[$membership->getType()] = 0;
            }

            if(!array_key_exists($membership->getStudent()->getGender()->value, $genders)) {
                $genders[$membership->getStudent()->getGender()->value] = 0;
            }

            $types[$membership->getType()]++;
            $genders[$membership->getStudent()->getGender()->value]++;
        }

        return $this->renderWithMessages('lists/tuition.html.twig', [
            'tuition' => $tuition,
            'memberships' => $memberships,
            'exams' => $exams,
            'today' => $this->dateHelper->getToday(),
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Tuition::class),
            'types' => $types,
            'genders' => $genders
        ]);
    }

    #[Route(path: '/tuitions/{uuid}/export', name: 'export_tuition')]
    public function exportTuition(#[MapEntity(mapping: ['uuid' => 'uuid'])] Tuition $tuition, TuitionCsvExporter $tuitionCsvExporter): Response {
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
    public function exportStudyGroup(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudyGroup $studyGroup, StudyGroupCsvExporter $csvExporter): Response {
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
    public function exportLms(#[MapEntity(mapping: ['lms' => 'uuid'])] LearningManagementSystem $lms, #[MapEntity(mapping: ['studyGroup' => 'uuid'])] StudyGroup $studyGroup, LearningManagementSystemInfoCsvExporter $exporter): Response {
        return $exporter->getCsvResponse($lms, $studyGroup);
    }
}