<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Entity\LessonAttendance as LessonAttendanceEntity;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;

abstract class AbstractResolver {
    private $attendanceRepository;
    private $excuseNoteRepository;

    public function __construct(LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository) {
        $this->attendanceRepository = $attendanceRepository;
        $this->excuseNoteRepository = $excuseNoteRepository;
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
            for($lesson = $excuseNote->getLessonStart(); $lesson <= $excuseNote->getLessonEnd(); $lesson++) {
                $key = sprintf('%s-%d', $excuseNote->getDate()->format('Y-m-d'), $lesson);

                if(!isset($collection[$key])) {
                    $collection[$key] = new ExcuseCollection($excuseNote->getDate(), $lesson);
                }

                $collection[$key]->add($excuseNote);
            }
        }

        return $collection;
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