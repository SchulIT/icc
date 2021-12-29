<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonRepositoryInterface;
use App\Settings\TimetableSettings;
use App\Sorting\LessonAttendenceStrategy;
use App\Sorting\Sorter;
use App\Entity\LessonAttendance as LessonAttendanceEntity;

class StudentInfoResolver extends AbstractResolver {

    private $commentRepository;
    private $lessonRepository;
    private $sorter;

    public function __construct(LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                TimetableSettings $timetableSettings, BookCommentRepositoryInterface $commentRepository, LessonRepositoryInterface $lessonRepository,
                                Sorter $sorter) {
        parent::__construct($attendanceRepository, $excuseNoteRepository, $timetableSettings);
        $this->commentRepository = $commentRepository;
        $this->lessonRepository = $lessonRepository;
        $this->sorter = $sorter;
    }

    public function resolveStudentInfo(Student $student, ?Section $section, array $tuitions = []) {
        $late = $this->getAttendanceRepository()->findLateByStudent($student, $tuitions);
        $absent = $this->getAttendanceRepository()->findAbsentByStudent($student, $tuitions);
        $excuseNotes = $this->getExcuseNoteRepository()->findByStudent($student);

        $this->sorter->sort($late, LessonAttendenceStrategy::class);
        $this->sorter->sort($absent, LessonAttendenceStrategy::class);

        $excuseCollections = $this->computeExcuseCollections($excuseNotes);
        $lateAttendanceCollection = $this->computeAttendanceCollection($late, $excuseCollections);
        $absentAttendanceCollection = $this->computeAttendanceCollection($absent, $excuseCollections);
        $comments = [ ];
        if($section !== null) {
            $comments = $this->commentRepository->findAllByDateAndStudent($student, $section->getStart(), $section->getEnd());
        }

        return new StudentInfo(
            $student,
            $this->lessonRepository->countHoldLessons($tuitions, $student),
            $lateAttendanceCollection,
            $absentAttendanceCollection,
            $comments
        );
    }


}