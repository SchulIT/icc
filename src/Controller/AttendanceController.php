<?php

namespace App\Controller;

use App\Book\Student\StudentInfoResolver;
use App\Entity\LessonEntry;
use App\Entity\Teacher;
use App\Entity\User;
use App\Grouping\DateWeekOfYearStrategy;
use App\Grouping\Grouper;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Settings\BookSettings;
use App\Settings\TimetableSettings;
use App\Sorting\DateStrategy;
use App\Sorting\DateWeekOfYearGroupStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/attendance')]
class AttendanceController extends AbstractController {

    #[Route(name: 'student_attendance')]
    public function index(Request $request, StudentFilter $studentFilter, SectionFilter $sectionFilter,
                          BookSettings $bookSettings, TimetableSettings $timetableSettings,
                          TuitionRepositoryInterface $tuitionRepository, LessonEntryRepositoryInterface $entryRepository,
                          StudentInfoResolver $infoResolver, Sorter $sorter, Grouper $grouper, DateHelper $dateHelper,
                          LessonAttendanceRepositoryInterface $lessonAttendanceRepository): Response {
        if($bookSettings->isAttendanceVisibleForStudentsAndParentsEnabled() === false) {
            throw new AccessDeniedHttpException();
        }

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user, true);

        $info = null;
        $groups = [ ];
        $entries = [ ];

        if($sectionFilterView->getCurrentSection() !== null && $studentFilterView->getCurrentStudent() !== null) {
            $tuitions = $tuitionRepository->findAllByStudents([$studentFilterView->getCurrentStudent()], $sectionFilterView->getCurrentSection());
            $info = $infoResolver->resolveStudentInfo($studentFilterView->getCurrentStudent(), $sectionFilterView->getCurrentSection(), $tuitions);

            $min = $sectionFilterView->getCurrentSection()->getStart();
            $max = min(
                $dateHelper->getToday()->modify('-1 day'),
                $sectionFilterView->getCurrentSection()->getEnd()
            );

            foreach($tuitions as $tuition) {
                $entries = array_merge($entries, $entryRepository->findAllByTuition($tuition, $min, $max));
            }

            foreach($lessonAttendanceRepository->findByStudentAndDateRange($studentFilterView->getCurrentStudent(), $min, $max) as $attendance) {
                if($attendance->getEntry() !== null && !in_array($attendance->getEntry(), $entries)) {
                    $entries[] = $attendance->getEntry();
                }
            }

            /**
             * @var string $key
             * @var LessonEntry $entry
             */
            foreach($entries as $key => $entry) {
                $lesson = null;

                if($entry->getLesson() !== null) {
                    $lesson = [
                        'uuid' => $entry->getLesson()->getUuid()->toString(),
                        'date' => $entry->getLesson()->getDate()->format('c'),
                        'start' => $entry->getLesson()->getLessonStart(),
                        'end' => $entry->getLesson()->getLessonEnd(),
                        'teachers' => $entry->getLesson()->getTeachers()->map(fn(Teacher $teacher) => $teacher->getAcronym())->toArray(),
                        'subject' => $entry->getLesson()->getSubjectName()
                    ];
                }

                $entries[$key] = [
                    'uuid' => $entry->getUuid()->toString(),
                    'lesson' => $lesson,
                    'start' => $entry->getLessonStart(),
                    'end' => $entry->getLessonEnd(),
                    'is_cancelled' => $entry->isCancelled()
                ];

                if($entry->isCancelled()) {
                    $entries[$key]['cancel_reason'] = $entry->getCancelReason();
                }
            }

            $days = $this->getListOfDays($min, $max, $timetableSettings->getDays());
            $groups = $grouper->group($days, DateWeekOfYearStrategy::class);

            $sorter->sort($groups, DateWeekOfYearGroupStrategy::class, SortDirection::Descending);
            $sorter->sortGroupItems($groups, DateStrategy::class, SortDirection::Descending);
        }

        return $this->render('attendance/index.html.twig', [
            'info' => $info,
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'studentFilter' => $studentFilterView,
            'numberOfLessons' => $timetableSettings->getMaxLessons(),
            'entries' => $entries
        ]);
    }

    private function getListOfDays(DateTime $min, DateTime $max, array $days): array {
        $result = [ ];
        $current = clone $min;
        while($current <= $max) {
            if(in_array((int)$current->format('w'), $days)) {
                $result[] = clone $current;
            }
            $current = $current->modify('+1 day');
        }

        return $result;
    }
}