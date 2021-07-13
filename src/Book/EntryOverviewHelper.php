<?php

namespace App\Book;

use App\Entity\BookComment;
use App\Entity\Grade;
use App\Entity\LessonEntry;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Grouping\Grouper;
use App\Grouping\LessonDayStrategy;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\LessonDayGroupStrategy;
use App\Sorting\LessonStrategy;
use App\Sorting\Sorter;
use DateTime;

class EntryOverviewHelper {
    private $lessonRepository;
    private $tuitionRepository;
    private $entryRepository;
    private $commentRepository;
    private $attendanceRepository;

    private $sectionResolver;
    private $grouper;
    private $sorter;

    public function __construct(TimetableLessonRepositoryInterface $lessonRepository, TuitionRepositoryInterface $tuitionRepository,
                                LessonEntryRepositoryInterface $entryRepository, BookCommentRepositoryInterface $commentRepository,
                                LessonAttendanceRepositoryInterface $attendanceRepository, SectionResolverInterface $sectionResolver,
                                Grouper $grouper, Sorter $sorter) {
        $this->lessonRepository = $lessonRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->entryRepository = $entryRepository;
        $this->commentRepository = $commentRepository;
        $this->sectionResolver = $sectionResolver;
        $this->attendanceRepository = $attendanceRepository;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    public function computeOverviewForTuition(Tuition $tuition, DateTime $start, DateTime $end): EntryOverview {
        $entries = $this->entryRepository->findAllByTuition($tuition, $start, $end);
        $comments = $this->commentRepository->findAllByDateAndTuition($tuition, $start, $end);

        return $this->computeOverview([$tuition], $entries, $comments, $start, $end);
    }

    /**
     * @param Tuition[] $tuitions
     * @param LessonEntry[] $entries
     * @param BookComment[] $comments
     * @param DateTime $start
     * @param DateTime $end
     * @return EntryOverview
     */
    private function computeOverview(array $tuitions, array $entries, array $comments, DateTime $start, DateTime $end): EntryOverview {
        if($start > $end) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

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
                $key = sprintf('%s-%d-%s', $entry->getDate()->format('Y-m-d'), $lessonNumber, $entry->getTuition()->getUuid()->toString());

                $lesson = new Lesson(clone $entry->getDate(), $lessonNumber, null, $entry);
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

        $timetableLessons = $this->lessonRepository->findAllByTuitionsAndWeeks($tuitions, $weekNumbers);

        $current = clone $start;
        while($current < $end) {
            $dailyLessons = array_filter($timetableLessons, function(TimetableLesson $lesson) use ($current) {
                return $lesson->getDay() === (int)$current->format('N')
                        && in_array((int)$current->format('W'), $lesson->getWeek()->getWeeksAsIntArray())
                        && $lesson->getPeriod()->getStart() <= $current
                        && $lesson->getPeriod()->getEnd() >= $current;
            });

            foreach($dailyLessons as $dailyLesson) {
                for($lessonNumber = $dailyLesson->getLesson(); $lessonNumber <= $dailyLesson->getLesson() + ($dailyLesson->isDoubleLesson() ? 1 : 0); $lessonNumber++) {
                    $key = sprintf('%s-%d-%s', $current->format('Y-m-d'), $lessonNumber, $dailyLesson->getTuition()->getUuid()->toString());

                    if (!array_key_exists($key, $lessons)) {
                        $lessons[$key] = new Lesson(clone $current, $lessonNumber);
                    }

                    $lessons[$key]->setTimetableLesson($dailyLesson);
                }
            }

            $current = $current->modify('+1 day');
        }

        $groups = $this->grouper->group(array_values($lessons), LessonDayStrategy::class);
        $this->sorter->sort($groups, LessonDayGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, LessonStrategy::class);

        return new EntryOverview($start, $end, $groups, $comments);
    }

    public function computeOverviewForTeacher(Teacher $teacher, DateTime $start, DateTime $end): EntryOverview {
        $section = $this->sectionResolver->getSectionForDate($start);
        $entries = [ ];
        $tuitions = [ ];
        $comments = [ ];

        if($section !== null) {
            $tuitions = $this->tuitionRepository->findAllByTeacher($teacher, $section);

            foreach($tuitions as $tuition) {
                $entries = array_merge($entries, $this->entryRepository->findAllByTuition($tuition, $start, $end));
                $comments = array_merge($comments, $this->commentRepository->findAllByDateAndTuition($tuition, $start, $end));
            }
        }

        return $this->computeOverview($tuitions, $entries, $comments, $start, $end);
    }

    public function computeOverviewForGrade(Grade $grade, DateTime $start, DateTime $end): EntryOverview {
        $entries = $this->entryRepository->findAllByGrade($grade, $start, $end);
        $tuitions = $this->tuitionRepository->findAllByGrades([$grade]);

        $comments = [ ];
        $section = $this->sectionResolver->getSectionForDate($start);

        if($section !== null) {
            $comments = $this->commentRepository->findAllByDateAndGrade($grade, $section, $start, $end);
        }

        return $this->computeOverview($tuitions, $entries, $comments, $start, $end);
    }
}