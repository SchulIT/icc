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
use App\Section\SectionResolver;
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
     * @Route("/tuitions", name="list_tuitions")
     */
    public function tuitions(SectionFilter $sectionFilter, GradeFilter $gradeFilter, StudentFilter $studentFilter, TeacherFilter $teacherFilter, TuitionRepositoryInterface $tuitionRepository, Request $request) {
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
                $membership = $tuition->getStudyGroup()->getMemberships()->filter(function(StudyGroupMembership $membership) use ($studentFilterView) {
                    return $membership->getStudent()->getId() === $studentFilterView->getCurrentStudent()->getId();
                })->first();

                $memberships[$tuition->getExternalId()] = $membership->getType();
            }
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $tuitions = $tuitionRepository->findAllByGrades([$gradeFilterView->getCurrentGrade()]);
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

        $tuitions = array_filter($tuitions, function(Tuition $tuition) use ($sectionFilterView) {
            return $tuition->getSection() === $sectionFilterView->getCurrentSection();
        });

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

    /**
     * @Route("/tuitions/{uuid}", name="list_tuition")
     */
    public function tuition(Tuition $tuition, TuitionRepositoryInterface $tuitionRepository, ExamRepositoryInterface $examRepository) {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        $tuition = $tuitionRepository->findOneById($tuition->getId());
        $memberships = $tuition->getStudyGroup()->getMemberships()->toArray();
        $this->sorter->sort($memberships, StudentGroupMembershipStrategy::class);

        $exams = $examRepository->findAllByTuitions([$tuition], null, true);

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
     * @Route("/tuitions/{uuid}/export", name="export_tuition")
     */
    public function exportTuition(Tuition $tuition, TuitionCsvExporter $tuitionCsvExporter) {
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        return $tuitionCsvExporter->getCsvResponse($tuition);
    }

    /**
     * @Route("/study_groups", name="list_studygroups")
     */
    public function studyGroups(SectionFilter $sectionFilter, StudyGroupFilter $studyGroupFilter, StudentFilter $studentFilter,
                                TuitionRepositoryInterface $tuitionRepository, Request $request, Sorter $sorter) {
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

        $studyGroups = array_filter($studyGroups, function(StudyGroup $studyGroup) use ($sectionFilterView) {
            return $studyGroup->getSection() === $sectionFilterView->getCurrentSection();
        });

        $grade = null;
        $gradeTeachers = [ ];
        $substitutionalGradeTeachers = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null && $studyGroupFilterView->getCurrentStudyGroup()->getType()->equals(StudyGroupType::Grade())) {
            /** @var Grade $grade */
            $grade = $studyGroupFilterView->getCurrentStudyGroup()->getGrades()->first();
        } else if($studentFilterView->getCurrentStudent() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $grade = $studentFilterView->getCurrentStudent()->getGrade($sectionFilterView->getCurrentSection());
        }

        if($grade !== null) {
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

        $tuitions = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            if($studyGroupFilterView->getCurrentStudyGroup()->getType()->equals(StudyGroupType::Grade())) {
                $tuitions = $tuitionRepository->findAllByGrades($studyGroupFilterView->getCurrentStudyGroup()->getGrades()->toArray());
            } else {
                $tuitions = $studyGroupFilterView->getCurrentStudyGroup()->getTuitions()->toArray();
            }
        }

        $tuitions = array_filter($tuitions, function(Tuition $tuition) use ($sectionFilterView) {
            return $tuition->getSection() === $sectionFilterView->getCurrentSection();
        });

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
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(StudyGroup::class)
        ]);
    }

    /**
     * @Route("/study_groups/{uuid}/export", name="export_studygroup")
     */
    public function exportStudyGroup(StudyGroup $studyGroup, StudyGroupCsvExporter $csvExporter) {
        $this->denyAccessUnlessGranted(ListsVoter::StudyGroups);

        return $csvExporter->getCsvResponse($studyGroup);
    }

    /**
     * @Route("/teachers", name="list_teachers")
     */
    public function teachers(SubjectFilter $subjectFilter, TeacherTagFilter $tagFilter, TeacherRepositoryInterface $teacherRepository, Request $request) {
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
            'last_import' => $this->importDateTimeRepository->findOneByEntityClass(Teacher::class)
        ]);
    }

    /**
     * @param Teacher[] $teachers
     * @param TeacherTag $tag
     * @return Teacher[]
     */
    private function filterImplicitTeacherTag(array $teachers, TeacherTag $tag): array {
        return array_filter($teachers, function(Teacher $teacher) use ($tag) {
            /** @var GradeTeacher $gradeTeacher */
            foreach($teacher->getGrades() as $gradeTeacher) {
                if($gradeTeacher->getType()->equals(GradeTeacherType::Primary()) && $tag->getUuid()->toString() === TeacherTag::GradeTeacherTagUuid) {
                    return true;
                }

                if($gradeTeacher->getType()->equals(GradeTeacherType::Substitute()) && $tag->getUuid()->toString() === TeacherTag::SubstituteGradeTeacherTagUuid) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @Route("/privacy", name="list_privacy")
     */
    public function privacy(SectionResolver $sectionResolver, StudyGroupFilter $studyGroupFilter, Request $request, StudentRepositoryInterface $studentRepository, PrivacyCategoryRepositoryInterface $privacyCategoryRepository) {
        $this->denyAccessUnlessGranted(ListsVoter::Privacy);

        /** @var User $user */
        $user = $this->getUser();

        $q = $request->query->get('q', null);
        $studygroupView = $studyGroupFilter->handle($request->query->get('study_group', null), $sectionResolver->getCurrentSection(), $user);

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
            'section' => $sectionResolver->getCurrentSection(),
            'studyGroupFilter' => $studygroupView,
            'isStart' => $request->query->has('q') === false && $request->query->has('study_group') === false,
            'last_import_categories' => $this->importDateTimeRepository->findOneByEntityClass(PrivacyCategory::class),
            'last_import_students' => $this->importDateTimeRepository->findOneByEntityClass(Student::class)
        ]);
    }
}