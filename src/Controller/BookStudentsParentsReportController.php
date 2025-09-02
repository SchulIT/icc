<?php

namespace App\Controller;

use App\Book\EntryOverviewHelper;
use App\Book\Student\StudentInfoResolver;
use App\Entity\BookComment;
use App\Entity\BookEvent;
use App\Entity\LessonEntry;
use App\Entity\Teacher;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Grouping\DateWeekOfYearStrategy;
use App\Grouping\Grouper;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\BookEventRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Security\Voter\BookCommentVoter;
use App\Settings\BookSettings;
use App\Settings\TimetableSettings;
use App\Sorting\BookCommentDateStrategy;
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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/report')]
#[IsFeatureEnabled(Feature::Book)]
class BookStudentsParentsReportController extends AbstractController {

    use CalendarWeeksTrait;

    #[Route('/lessons', name: 'student_lessons_overview')]
    public function lessons(Request $request, StudentFilter $studentFilter, SectionFilter $sectionFilter,
                            BookSettings $bookSettings, EntryOverviewHelper $entryOverviewHelper,
                            Sorter $sorter, Grouper $grouper, DateHelper $dateHelper) {
        if($bookSettings->isLessonTopicsVisibleForStudentsAndParentsEnabled() === false) {
            throw new AccessDeniedHttpException();
        }

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user, true);

        $selectedDate = $this->resolveSelectedDate($request, $sectionFilterView->getCurrentSection(), $dateHelper);

        $overview = null;

        if($selectedDate !== null && $studentFilterView->getCurrentStudent() !== null) {
            $overview = $entryOverviewHelper->computeOverviewForStudentWithoutComment($studentFilterView->getCurrentStudent(), $selectedDate, (clone $selectedDate)->modify('+6 days'));
        }

        $weekStarts = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $weekStarts = $this->listCalendarWeeks($sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        }

        return $this->render('books/student_and_parents/lesson_topics.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'studentFilter' => $studentFilterView,
            'overview' => $overview,
            'weekStarts' => $weekStarts,
            'selectedDate' => $selectedDate
        ]);
    }

    #[Route('/comments', name: 'student_comments')]
    public function comments(Request $request, SectionFilter $sectionFilter, BookSettings $bookSettings,
                             #[CurrentUser] User $user, BookCommentRepositoryInterface $commentRepository, Sorter $sorter): Response {
        if($bookSettings->getStudentsAndParentsCanViewBookCommentsEnabled() === false) {
            throw new AccessDeniedHttpException();
        }

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));

        $comments = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            foreach($user->getStudents() as $student) {
                $comments = array_merge($comments, $commentRepository->findAllByDateAndStudent($student, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd()));
            }

            $comments = array_filter($comments, fn(BookComment $comment) => $this->isGranted(BookCommentVoter::View, $comment));
        }

        $sorter->sort($comments, BookCommentDateStrategy::class, SortDirection::Descending);

        return $this->render('books/student_and_parents/comments.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'comments' => $comments
        ]);
    }

    #[Route('/attendance', name: 'student_attendance')]
    public function attendance(Request $request, StudentFilter $studentFilter, SectionFilter $sectionFilter,
                               BookSettings $bookSettings, TimetableSettings $timetableSettings,
                               TuitionRepositoryInterface $tuitionRepository, LessonEntryRepositoryInterface $entryRepository,
                               StudentInfoResolver $infoResolver, Sorter $sorter, Grouper $grouper, DateHelper $dateHelper,
                               LessonAttendanceRepositoryInterface $lessonAttendanceRepository, BookEventRepositoryInterface $bookEventRepository): Response {
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
        $events = [ ];

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

            $events = $bookEventRepository->findByStudent($studentFilterView->getCurrentStudent(), $min, $max);

            foreach($lessonAttendanceRepository->findByStudentAndDateRange($studentFilterView->getCurrentStudent(), $min, $max, true) as $attendance) {
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

            /**
             * @var int $key
             * @var BookEvent $event
             */
            foreach($events as $key => $event) {
                $events[$key] = [
                    'uuid' => $event->getUuid()->toString(),
                    'date' => $event->getDate()->format('c'),
                    'start' => $event->getLessonStart(),
                    'end' => $event->getLessonEnd(),
                    'teacher' => $event->getTeacher()->getAcronym(),
                    'title' => $event->getTitle()
                ];
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
            'entries' => $entries,
            'events' => $events
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