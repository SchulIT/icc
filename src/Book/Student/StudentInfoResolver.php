<?php

namespace App\Book\Student;

use App\Entity\LessonAttendanceType;
use App\Entity\Section;
use App\Entity\Student;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\LessonAttendenceStrategy;
use App\Sorting\Sorter;

class StudentInfoResolver extends AbstractResolver {

    public function __construct(LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                ExcuseCollectionResolver $excuseCollectionResolver, private readonly BookCommentRepositoryInterface $commentRepository, private readonly TimetableLessonRepositoryInterface $lessonRepository,
                                private readonly Sorter $sorter) {
        parent::__construct($attendanceRepository, $excuseNoteRepository, $excuseCollectionResolver);
    }

    public function resolveStudentInfo(Student $student, ?Section $section, array $tuitions = []): StudentInfo {
        $attendances = $this->getAttendanceRepository()->findByStudent($student, $tuitions);

        $late = array_filter($attendances, fn(\App\Entity\LessonAttendance $a) => $a->getType() === LessonAttendanceType::Late);
        $absent = array_filter($attendances, fn(\App\Entity\LessonAttendance $a) => $a->getType() === LessonAttendanceType::Absent);
        $present = array_filter($attendances, fn(\App\Entity\LessonAttendance $a) => $a->getType() === LessonAttendanceType::Present);
        $excuseNotes = $this->getExcuseNoteRepository()->findByStudent($student);

        $this->sorter->sort($late, LessonAttendenceStrategy::class);
        $this->sorter->sort($absent, LessonAttendenceStrategy::class);

        $excuseCollections = $this->computeExcuseCollections($excuseNotes);
        $lateAttendanceCollection = $this->computeAttendanceCollectionWithoutExcuses($late);
        $absentAttendanceCollection = $this->computeAttendanceCollection($absent, $excuseCollections);
        $presentAttendanceCollection = $this->computeAttendanceCollectionWithoutExcuses($present);
        $comments = [ ];
        if($section !== null) {
            $comments = $this->commentRepository->findAllByDateAndStudent($student, $section->getStart(), $section->getEnd());
        }

        return new StudentInfo(
            $student,
            $this->lessonRepository->countHoldLessons($tuitions, $student),
            $lateAttendanceCollection,
            $absentAttendanceCollection,
            $presentAttendanceCollection,
            $comments,
            $this->computeAttendanceFlagCounts($attendances)
        );
    }


}