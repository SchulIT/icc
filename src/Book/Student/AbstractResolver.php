<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Entity\Attendance as LessonAttendanceEntity;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Settings\TimetableSettings;

abstract class AbstractResolver {
    public function __construct(private readonly LessonAttendanceRepositoryInterface $attendanceRepository, private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository, private readonly ExcuseCollectionResolver $excuseCollectionResolver)
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
        // for compatibility reasons only -> merge with subclasses!
        return $this->excuseCollectionResolver->resolve($excuseNotes);
    }

    /**
     * @param LessonAttendanceEntity[] $attendances
     * @return LessonAttendance[]
     */
    protected function computeAttendanceCollectionWithoutExcuses(array $attendances): array {
        $lessonAttendance = [ ];

        foreach($attendances as $attendance) {
            $excuses = new ExcuseCollection($attendance->getEntry()->getLesson()->getDate(), $attendance->getEntry()->getLessonStart());
            $lessonAttendance[] = new LessonAttendance($attendance->getEntry()->getLesson()->getDate(), $attendance->getLesson(), $attendance, $excuses);
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
            $key = sprintf('%s-%d', $attendance->getEntry()->getLesson()->getDate()->format('Y-m-d'), $attendance->getLesson());
            $excuses = new ExcuseCollection($attendance->getEntry()->getLesson()->getDate(), $attendance->getLesson());

            if(isset($excuseCollection[$key])) {
                $excuses = $excuseCollection[$key];
            }

            $lessonAttendance[] = new LessonAttendance($attendance->getEntry()->getLesson()->getDate(), $attendance->getLesson(), $attendance, $excuses);
        }

        return $lessonAttendance;
    }

    /**
     * @param LessonAttendanceEntity[] $attendances
     * @return AttendanceFlagCount[]
     */
    protected function computeAttendanceFlagCounts(array $attendances): array {
        $counts = [ ];
        $flags = [ ];

        foreach($attendances as $attendance) {
            foreach($attendance->getFlags() as $flag) {
                $id = $flag->getId();

                if(!array_key_exists($id, $counts)) {
                    $counts[$id] = 0;
                }

                if(!array_key_exists($id, $flags)) {
                    $flags[$id] = $flag;
                }

                $counts[$id]++;
            }
        }

        $result = [ ];

        foreach($flags as $flag) {
            $result[] = new AttendanceFlagCount($flag, $counts[$flag->getId()]);
        }

        return $result;
    }
}