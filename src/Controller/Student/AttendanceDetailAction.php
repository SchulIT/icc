<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Book\Student\StudentStatisticsCounterResolver;
use App\Entity\Student;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Feature\IsFeatureEnabled;
use App\Repository\LessonAttendanceFlagRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Security\Voter\ListsVoter;
use App\Security\Voter\StudentVoter;
use App\Sorting\Sorter;
use App\Sorting\TuitionStrategy;
use App\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[IsFeatureEnabled(Feature::Book)]
class AttendanceDetailAction extends AbstractController {
    #[Route('/student/{uuid}/attendance', name: 'student_attendance_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        LessonAttendanceFlagRepositoryInterface $attendanceFlagRepository,
        TuitionRepositoryInterface $tuitionRepository,
        Sorter $sorter,
        StudentStatisticsCounterResolver $studentStatisticsCounterResolver,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);
        $this->denyAccessUnlessGranted('ROLE_BOOK_VIEWER');

        $sectionFilterView = $sectionFilter->handle($section);
        $attendanceFlags = $attendanceFlagRepository->findAll();
        $tuitions = $tuitionRepository->findAllByStudents([ $student ], $sectionFilterView->getCurrentSection());
        $sorter->sort($tuitions, TuitionStrategy::class);

        $counter = $studentStatisticsCounterResolver->resolve($student, $sectionFilterView->getCurrentSection(), $tuitions);

        $tuitionCounters = [ ];
        foreach($tuitions as $tuition) {
            $tuitionCounters[$tuition->getId()] = $studentStatisticsCounterResolver->resolve($student, $sectionFilterView->getCurrentSection(), [ $tuition ]);
        }

        return $this->render('student/attendance.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView,
            'attendanceFlags' => $attendanceFlags,
            'studentInfo' => $counter,
            'tuitions' => $tuitions,
            'tuitionCounters' => $tuitionCounters,
        ]);
    }
}
