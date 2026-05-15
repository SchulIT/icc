<?php

namespace App\Book;

use App\Book\Entity\BookComment;
use App\Book\Entity\BookEvent;
use App\Common\Entity\Grade;
use App\Book\Entity\LessonEntry;
use App\Common\Entity\Student as StudentEntity;
use App\Substitution\Entity\Substitution;
use App\Common\Entity\Teacher;
use App\Timetable\Entity\TimetableLesson;
use App\Common\Entity\Tuition;
use App\Framework\Grouping\Grouper;
use App\Book\Grouping\LessonDayStrategy;
use App\Appointment\Repository\AppointmentCategoryRepositoryInterface;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Book\Repository\BookCommentRepositoryInterface;
use App\Book\Repository\BookEventRepositoryInterface;
use App\Substitution\Repository\FreeTimespanRepositoryInterface;
use App\Book\Repository\LessonAttendanceRepositoryInterface;
use App\Book\Repository\LessonEntryRepositoryInterface;
use App\Substitution\Repository\SubstitutionRepositoryInterface;
use App\Timetable\Repository\TimetableLessonRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Timetable\Settings\TimetableSettings;
use App\Book\Sorting\BookCommentDateStrategy;
use App\Book\Sorting\LessonDayGroupStrategy;
use App\Book\Sorting\LessonStrategy;
use App\Framework\Sorting\SortDirection;
use App\Framework\Sorting\Sorter;
use App\Framework\Utils\ArrayUtils;
use DateTime;
use Egulias\EmailValidator\Parser\CommentStrategy\CommentStrategy;

class EntryOverviewHelper {
    public function __construct(private TimetableLessonRepositoryInterface $lessonRepository, private TuitionRepositoryInterface $tuitionRepository, private LessonEntryRepositoryInterface $entryRepository, private BookCommentRepositoryInterface $commentRepository, private LessonAttendanceRepositoryInterface $attendanceRepository, private SectionResolverInterface $sectionResolver, private TimetableSettings $timetableSettings, private AppointmentCategoryRepositoryInterface $appointmentCategoryRepository, private AppointmentRepositoryInterface $appointmentRepository, private FreeTimespanRepositoryInterface $freeTimespanRepository, private SubstitutionRepositoryInterface $substitutionRepository, private readonly BookEventRepositoryInterface $bookEventRepository, private Grouper $grouper, private Sorter $sorter)
    {
    }

    public function computeOverviewForTuition(Tuition $tuition, DateTime $start, DateTime $end): EntryOverview {
        $entries = $this->entryRepository->findAllByTuition($tuition, $start, $end);
        $comments = $this->commentRepository->findAllByDateAndTuition($tuition, $start, $end);

        return $this->computeOverview([$tuition], $entries, $comments, [ ], [ ], $start, $end);
    }

    /**
     * @param Tuition[] $tuitions
     * @param LessonEntry[] $entries
     * @param BookComment[] $comments
     * @param Substitution[] $substitutions
     * @param BookEvent[] $events
     */
    private function computeOverview(array $tuitions, array $entries, array $comments, array $substitutions, array $events, DateTime $start, DateTime $end): EntryOverview {
        if($start > $end) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        $tuitions = array_filter($tuitions, fn(Tuition $tuition) => $tuition->isBookEnabled());

        $lessons = [ ];

        foreach($entries as $entry) {
            if($entry->getTuition() === null) {
                // discard entries without tuition
                continue;
            }

            $presentCount = $this->attendanceRepository->countPresent($entry);
            $absentCount = $this->attendanceRepository->countAbsent($entry);
            $lateCount = $this->attendanceRepository->countLate($entry);

            for($lessonNumber = $entry->getLessonStart(); $lessonNumber <= $entry->getLessonEnd(); $lessonNumber++) {
                $key = sprintf('%s-%d-%s', $entry->getLesson()->getDate()->format('Y-m-d'), $lessonNumber, $entry->getTuition()->getUuid()->toString());

                $lesson = new Lesson(clone $entry->getLesson()->getDate(), $lessonNumber, null, $entry);
                $lesson->setAbsentCount($absentCount);
                $lesson->setPresentCount($presentCount);
                $lesson->setLateCount($lateCount);

                $lessons[$key] = $lesson;
            }
        }

        $weekNumbers = [ ];
        $currentWeek = clone $start;
        while($currentWeek < $end) {
            $weekNumber = (int)$currentWeek->format('W');

            if(!in_array($weekNumber, $weekNumbers)) {
                $weekNumbers[] = $weekNumber;
            }

            $currentWeek = $currentWeek->modify('+7 days');
        }

        $timetableLessons = $this->lessonRepository->findAllByTuitions($start, $end, $tuitions);

        $current = clone $start;
        while($current <= $end) {
            $dailyLessons = array_filter($timetableLessons, fn(TimetableLesson $lesson) => $lesson->getDate() == $current);

            foreach($dailyLessons as $dailyLesson) {
                for($lessonNumber = $dailyLesson->getLessonStart(); $lessonNumber <= $dailyLesson->getLessonEnd(); $lessonNumber++) {
                    $key = sprintf('%s-%d-%s', $current->format('Y-m-d'), $lessonNumber, $dailyLesson->getTuition()->getUuid()->toString());

                    if (!array_key_exists($key, $lessons)) {
                        $lessons[$key] = new Lesson(clone $current, $lessonNumber);
                    }

                    $lessons[$key]->setLesson($dailyLesson);
                }
            }

            $current = $current->modify('+1 day');
        }

        foreach($substitutions as $substitution) {
            $tuition = $this->tuitionRepository->findOneBySubstitution($substitution, $this->sectionResolver->getSectionForDate($substitution->getDate()));

            if($tuition === null) {
                continue;
            }

            $timetableLessons = $this->lessonRepository->findAllByTuitions($substitution->getDate(), $substitution->getDate(), [$tuition]);

            // Filter correct lesson
            foreach($timetableLessons as $dailyLesson) {
                for($lessonNumber = $dailyLesson->getLessonStart(); $lessonNumber <= $dailyLesson->getLessonEnd(); $lessonNumber++) {
                    if($substitution->getLessonStart() <= $lessonNumber && $lessonNumber <= $substitution->getLessonEnd()) {
                        $key = sprintf('%s-%d-%s', $substitution->getDate()->format('Y-m-d'), $lessonNumber, $dailyLesson->getTuition()->getUuid()->toString());

                        if (!array_key_exists($key, $lessons)) {
                            $lessons[$key] = new Lesson($substitution->getDate(), $lessonNumber, $dailyLesson, null, $substitution);
                        }

                        // Set correct entry
                        /** @var LessonEntry $lessonEntry */
                        foreach($dailyLesson->getEntries() as $lessonEntry) {
                            if($lessonEntry->getLessonStart() <= $lessonNumber && $lessonNumber <= $lessonEntry->getLessonEnd()) {
                                $lessons[$key]->setEntry($lessonEntry);
                                break;
                            }
                        }

                        if($lessons[$key]->getLesson() === null) {
                            $lessons[$key]->setLesson($dailyLesson);
                        }
                    }
                }
            }
        }

        $groups = $this->grouper->group(array_values($lessons), LessonDayStrategy::class);
        $this->sorter->sort($groups, LessonDayGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, LessonStrategy::class);
        $this->sorter->sort($comments, BookCommentDateStrategy::class, SortDirection::Descending);;

        $freeTimespans = $this->computeFreeTimespans($start, $end);

        return new EntryOverview($start, $end, $groups, $comments, $events, $freeTimespans);
    }

    public function computeOverviewForTeacher(Teacher $teacher, DateTime $start, DateTime $end): EntryOverview {
        $section = $this->sectionResolver->getSectionForDate($start);
        $entries = [ ];
        $tuitions = [ ];
        $comments = [ ];

        if($section !== null) {
            $entries = $this->entryRepository->findAllBySubstituteTeacher($teacher, $start, $end);
            $tuitions = $this->tuitionRepository->findAllByTeacher($teacher, $section);

            foreach($tuitions as $tuition) {
                $entries = array_merge($entries, $this->entryRepository->findAllByTuition($tuition, $start, $end));
                $comments = array_merge($comments, $this->commentRepository->findAllByDateAndTuition($tuition, $start, $end));
            }

            $comments = ArrayUtils::unique($comments);
        }

        $substitutions = [ ];

        $current = clone $start;
        while($current <= $end) {
            $substitutions = array_merge(
                $substitutions,
                $this->substitutionRepository->findAllForTeacher($teacher, $current)
            );
            $current = $current->modify('+1 day');
        }

        $substitutions = array_filter($substitutions, fn(Substitution $substitution) => $substitution->getReplacementTeachers()->contains($teacher));
        $events = $this->bookEventRepository->findByTeacher($teacher, $start, $end);

        return $this->computeOverview($tuitions, $entries, $comments, $substitutions, $events, $start, $end);
    }

    public function computeOverviewForGrade(Grade $grade, DateTime $start, DateTime $end): EntryOverview {
        $section = $this->sectionResolver->getSectionForDate($start);

        if($section === null) {
            return new EntryOverview($start, $end, [ ], [ ], [ ], [ ]);
        }

        if($end > $section->getEnd()) {
            $end = $section->getEnd();
        }

        $entries = $this->entryRepository->findAllByGrade($grade, $start, $end);
        $tuitions = $this->tuitionRepository->findAllByGrades([$grade], $section);

        $comments = [ ];
        $events = [ ];
        $section = $this->sectionResolver->getSectionForDate($start);

        if($section !== null) {
            $comments = $this->commentRepository->findAllByDateAndGrade($grade, $section, $start, $end);
            $events = $this->bookEventRepository->findByGrade($grade, $section, $start, $end);
        }

        return $this->computeOverview($tuitions, $entries, $comments, [ ], $events, $start, $end);
    }

    public function computeOverviewForStudentWithoutComment(StudentEntity $student, DateTime $start, DateTime $end): EntryOverview {
        $section = $this->sectionResolver->getSectionForDate($start);

        if($section === null) {
            return new EntryOverview($start, $end, [ ], [ ], [ ], [ ]);
        }

        if($end > $section->getEnd()) {
            $end = $section->getEnd();
        }

        $entries = $this->entryRepository->findAllByStudents($student, $start, $end);
        $tuitions = $this->tuitionRepository->findAllByStudents([$student], $section);

        $events = [ ];
        $section = $this->sectionResolver->getSectionForDate($start);

        if($section !== null) {
            $events = $this->bookEventRepository->findByStudent($student, $start, $end);
        }

        return $this->computeOverview($tuitions, $entries, [ ], [ ], $events, $start, $end);
    }

    /**
     * @return FreeTimespan[]
     */
    public function computeFreeTimespans(DateTime $start, DateTime $end): array {
        $result = [ ];

        $categories = array_map(fn(int $id) => $this->appointmentCategoryRepository->findOneById($id), $this->timetableSettings->getCategoryIds());

        if(count($categories) > 0) {
            $appointments = $this->appointmentRepository->findAllStartEnd($start, $end, $categories);

            foreach($appointments as $appointment) {
                $current = clone $appointment->getStart();
                while($current < $appointment->getEnd()) {
                    $result[] = new FreeTimespan(clone $current, 1, $this->timetableSettings->getMaxLessons(), $appointment->getTitle());
                    $current = $current->modify('+1 day');
                }
            }
        }

        // findAllStartEnd queries appointments which includes the end date - but that may cause issues when $start==$end, so manually filter
        $result = array_filter($result, fn(FreeTimespan $timespan) => $timespan->getDate() >= $start && $timespan->getDate() <= $end);

        $current = clone $start;
        while($current <= $end) {
            $freeTimespans = $this->freeTimespanRepository->findAllByDate($current);

            foreach($freeTimespans as $timespan) {
                $result[] = new FreeTimespan(clone $timespan->getDate(), $timespan->getStart(), $timespan->getEnd(), 'Vertretungsplan');
            }

            $current = $current->modify('+1 day');
        }

        return $result;
    }
}