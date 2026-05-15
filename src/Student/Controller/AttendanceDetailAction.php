<?php

declare(strict_types=1);

namespace App\Student\Controller;

use App\Book\Statistics\StudentTimetableAttendanceStatisticsGenerator;
use App\Book\Student\StudentStatisticsCounterResolver;
use App\Common\Entity\Student;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Book\Repository\LessonAttendanceFlagRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Voter\ListsVoter;
use App\Common\Voter\StudentVoter;
use App\Timetable\Settings\TimetableSettings;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\TuitionStrategy;
use App\Common\View\Filter\SectionFilter;
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
        StudentTimetableAttendanceStatisticsGenerator $studentTimetableAttendanceStatisticsGenerator,
        TimetableSettings $timetableSettings,
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

        $timetableAttendanceStatistics = $studentTimetableAttendanceStatisticsGenerator->getCount($student, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());

        return $this->render('student/attendance.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView,
            'attendanceFlags' => $attendanceFlags,
            'studentInfo' => $counter,
            'tuitions' => $tuitions,
            'tuitionCounters' => $tuitionCounters,
            'timetableAttendanceStatistics' => $timetableAttendanceStatistics,
            'maxLessons' => $timetableSettings->getMaxLessons(),
            'timetableDays' => $timetableSettings->getDays(),
        ]);
    }
}
