<?php

namespace App\Book\Student;

use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\Section;
use App\Entity\Student;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\BookEventRepositoryInterface;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Sorting\AttendanceStrategy;
use App\Sorting\Sorter;

class StudentInfoResolver extends AbstractResolver {

    public function __construct(LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                ExcuseCollectionResolver $excuseCollectionResolver, private readonly BookCommentRepositoryInterface $commentRepository, private readonly TimetableLessonRepositoryInterface $lessonRepository,
                                private readonly Sorter $sorter) {
        parent::__construct($attendanceRepository, $excuseNoteRepository, $excuseCollectionResolver);
    }

    public function resolveStudentInfo(Student $student, Section $section, array $tuitions = [], bool $includeEvents = false): StudentInfo {
        $attendances = $this->getAttendanceRepository()->findByStudent($student, $section->getStart(), $section->getEnd(), $includeEvents, $tuitions);

        $eventLessonCount = 0;
        if($includeEvents === true) {
            $eventAttendances = $this->getAttendanceRepository()->findByStudentEvents($student, $section->getStart(), $section->getEnd());
            $eventLessonCount = count($eventAttendances);
        }

        $late = array_filter($attendances, fn(Attendance $a) => $a->getType() === AttendanceType::Late);
        $absent = array_filter($attendances, fn(Attendance $a) => $a->getType() === AttendanceType::Absent);
        $present = array_filter($attendances, fn(Attendance $a) => $a->getType() === AttendanceType::Present);
        $excuseNotes = $this->getExcuseNoteRepository()->findByStudent($student);

        $this->sorter->sort($late, AttendanceStrategy::class);
        $this->sorter->sort($absent, AttendanceStrategy::class);

        $excuseCollections = $this->computeExcuseCollections($excuseNotes);
        $lateAttendanceCollection = $this->computeAttendanceCollectionWithoutExcuses($late);
        $absentAttendanceCollection = $this->computeAttendanceCollection($absent, $excuseCollections);
        $presentAttendanceCollection = $this->computeAttendanceCollectionWithoutExcuses($present);
        $comments = $this->commentRepository->findAllByDateAndStudent($student, $section->getStart(), $section->getEnd());

        return new StudentInfo(
            $student,
            $this->lessonRepository->countHoldLessons($tuitions, $student) + $eventLessonCount,
            $lateAttendanceCollection,
            $absentAttendanceCollection,
            $presentAttendanceCollection,
            $comments,
            $this->computeAttendanceFlagCounts($attendances)
        );
    }


}