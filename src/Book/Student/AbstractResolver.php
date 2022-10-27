<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Entity\LessonAttendance as LessonAttendanceEntity;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Settings\TimetableSettings;

abstract class AbstractResolver {
    public function __construct(private LessonAttendanceRepositoryInterface $attendanceRepository, private ExcuseNoteRepositoryInterface $excuseNoteRepository, private TimetableSettings $timetableSettings)
    {
    }

    protected function getAttendanceRepository(): LessonAttendanceRepositoryInterface {
        return $this->attendanceRepository;
    }

    protected function getExcuseNoteRepository(): ExcuseNoteRepositoryInterface {
        return $this->excuseNoteRepository;
    }

    /**
     * @param ExcuseNote[] $excuseNotes
     * @return ExcuseCollection[]
     */
    protected function computeExcuseCollections(array $excuseNotes): array {
        /** @var ExcuseCollection[] $collection */
        $collection = [ ];

        foreach($excuseNotes as $excuseNote) {
            for($date = clone $excuseNote->getFrom()->getDate(); $date <= $excuseNote->getUntil()->getDate(); $date->modify('+1 day')) {
                for($lesson = ($date == $excuseNote->getFrom()->getDate() ? $excuseNote->getFrom()->getLesson() : 1);
                    $lesson <= ($date == $excuseNote->getUntil()->getDate() ? $excuseNote->getUntil()->getLesson() : $this->timetableSettings->getMaxLessons());
                    $lesson++
                ) {
                    $key = sprintf('%s-%d', $date->format('Y-m-d'), $lesson);

                    if(!isset($collection[$key])) {
                        $collection[$key] = new ExcuseCollection($date, $lesson);
                    }

                    $collection[$key]->add($excuseNote);
                }
            }
        }

        return $collection;
    }

    /**
     * @param LessonAttendanceEntity[] $attendances
     * @return LessonAttendance[]
     */
    protected function computeAttendanceCollectionWithoutExcuses(array $attendances): array {
        $lessonAttendance = [ ];

        foreach($attendances as $attendance) {
            $excuses = new ExcuseCollection($attendance->getEntry()->getLesson()->getDate(), $attendance->getEntry()->getLessonStart());
            $lessonAttendance[] = new LessonAttendance($attendance->getEntry()->getLesson()->getDate(), $attendance->getEntry()->getLessonStart(), $attendance, $excuses);
        }

        return $lessonAttendance;
    }

    /**
     * @param LessonAttendanceEntity[] $attendances
     * @param ExcuseCollection[] $excuseCollection
     * @return LessonAttendance[]
     */
    protected function computeAttendanceCollection(array $attendances, array $excuseCollection): array {
        $lessonAttendance = [ ];

        foreach($attendances as $attendance) {
            for($lesson = $attendance->getEntry()->getLessonStart() + ($attendance->getEntry()->getLessonEnd() - $attendance->getEntry()->getLessonStart() - $attendance->getAbsentLessons() + 1); $lesson <= $attendance->getEntry()->getLessonEnd(); $lesson++) {
                $key = sprintf('%s-%d', $attendance->getEntry()->getLesson()->getDate()->format('Y-m-d'), $lesson);

                $excuses = new ExcuseCollection($attendance->getEntry()->getLesson()->getDate(), $lesson);

                if(isset($excuseCollection[$key])) {
                    $excuses = $excuseCollection[$key];
                }

                $lessonAttendance[] = new LessonAttendance($attendance->getEntry()->getLesson()->getDate(), $lesson, $attendance, $excuses);
            }
        }

        return $lessonAttendance;
    }
}