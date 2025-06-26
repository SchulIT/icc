<?php

namespace App\Controller;

use App\Book\Student\StudentInfoResolver;
use App\Entity\GradeTeacher;
use App\Entity\Student;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Grouping\Grouper;
use App\Grouping\StudentGradeStrategy;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\StudentInformationRepositoryInterface;
use App\Repository\LessonAttendanceFlagRepositoryInterface;
use App\Repository\PrivacyCategoryRepositoryInterface;
use App\Repository\ReturnItemRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\ListsVoter;
use App\Security\Voter\StudentVoter;
use App\Sorting\BookCommentDateStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\TeacherStrategy;
use App\Sorting\TuitionStrategy;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student')]
#[IsGranted(StudentVoter::ShowAny)]
class StudentController extends AbstractController {
    #[Route(name: 'students')]
    public function index(#[CurrentUser] User $user, StudentRepositoryInterface $studentRepository, StudentFilter $studentFilter, SectionFilter $sectionFilter, Request $request, Grouper $grouper, Sorter $sorter): Response {
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user);

        $students = [ ];
        $groups = [ ];

        if($sectionFilterView->getCurrentSection() !== null && $studentFilterView->getCurrentStudent() !== null) {
            return $this->redirectToRoute('show_student', [
                'uuid' => $studentFilterView->getCurrentStudent()->getUuid(),
                'section' => $sectionFilterView->getCurrentSection()->getUuid()
            ]);
        }

        if($sectionFilterView->getCurrentSection() !== null) {
            $students = $studentRepository->findAllBySection($sectionFilterView->getCurrentSection());
            $groups = $grouper->group($students, StudentGradeStrategy::class, [ 'section' => $sectionFilterView->getCurrentSection() ]);
            $sorter->sort($groups, StudentGradeGroupStrategy::class);
            $sorter->sortGroupItems($groups, StudentStrategy::class);
        }

        return $this->render('student/index.html.twig', [
            'students' => $students,
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'studentFilter' => $studentFilterView,
        ]);
    }

    #[Route('/{uuid}', name: 'show_student')]
    public function student(#[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student, BookCommentRepositoryInterface $bookCommentRepository,
                            StudentInformationRepositoryInterface             $bookStudentInformationRepository,
                            ReturnItemRepositoryInterface                     $returnItemRepository,
                            StudentInfoResolver                               $studentInfoResolver,
                            LessonAttendanceFlagRepositoryInterface           $attendanceFlagRepository,
                            TuitionRepositoryInterface                        $tuitionRepository,
                            PrivacyCategoryRepositoryInterface                $privacyCategoryRepository,
                            SectionFilter                                     $sectionFilter,
                            FeatureManager                                    $featureManager,
                            Sorter                                            $sorter,
                            Request                                           $request): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));

        $comments = [ ];
        $lessonInfo = [ ];
        $gradeTeachers = [ ];
        $tuitions = [ ];
        $attendanceFlags = $attendanceFlagRepository->findAll();
        $studentInfo = null;
        $returnItems = [ ];
        $privacyCategories = [ ];
        $grade = null;

        if($this->isGranted(ListsVoter::Privacy) && $featureManager->isFeatureEnabled(Feature::Privacy)) {
            $privacyCategories = $privacyCategoryRepository->findAll();
        }

        if($sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllByStudents([ $student ], $sectionFilterView->getCurrentSection());
            $sorter->sort($tuitions, TuitionStrategy::class);

            if($this->isGranted('ROLE_BOOK_VIEWER') && $featureManager->isFeatureEnabled(Feature::Book)) {
                $comments = $bookCommentRepository->findAllByDateAndStudent($student, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
                $sorter->sort($comments, BookCommentDateStrategy::class, SortDirection::Descending);
            }

            $grade = $student->getGrade($sectionFilterView->getCurrentSection());

            if($grade !== null) {
                $gradeTeachers = $grade
                    ->getTeachers()
                    ->filter(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getSection()->getId() === $sectionFilterView->getCurrentSection()->getId())
                    ->map(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getTeacher())
                    ->toArray();

                $sorter->sort($gradeTeachers, TeacherStrategy::class);
            }

            if($this->isGranted('ROLE_BOOK_VIEWER') && $featureManager->isFeatureEnabled(Feature::Book)) {
                $studentInfo = $studentInfoResolver->resolveStudentInfo($student, $sectionFilterView->getCurrentSection(), $tuitions);
                $lessonInfo = $bookStudentInformationRepository->findByStudents([$student], null, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
            }

            if($this->isGranted('ROLE_RETURN_ITEM_CREATOR') && $featureManager->isFeatureEnabled(Feature::ReturnItem)) {
                $returnItems = $returnItemRepository->findByStudent($student, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
            }
        }

        return $this->render('student/show.html.twig', [
            'student' => $student,
            'grade' => $grade,
            'gradeTeachers' => $gradeTeachers,
            'comments' => $comments,
            'studentInfo' => $studentInfo,
            'sectionFilter' => $sectionFilterView,
            'attendanceFlags' => $attendanceFlags,
            'lessonInfo' => $lessonInfo,
            'returnItems' => $returnItems,
            'tuitions' => $tuitions,
            'privacyCategories' => $privacyCategories,
        ]);
    }
}