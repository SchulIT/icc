<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\BookCommentRepositoryInterface;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Sorting\LessonAttendenceStrategy;
use App\Sorting\Sorter;
use App\Entity\LessonAttendance as LessonAttendanceEntity;

class StudentInfoResolver extends AbstractResolver {

    private $commentRepository;
    private $sorter;

    public function __construct(LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                BookCommentRepositoryInterface $commentRepository, Sorter $sorter) {
        parent::__construct($attendanceRepository, $excuseNoteRepository);
        $this->commentRepository = $commentRepository;
        $this->sorter = $sorter;
    }

    public function resolveStudentInfo(Student $student, ?Section $section, ?Tuition $tuition = null) {
        $late = $this->getAttendanceRepository()->findLateByStudent($student, $tuition !== null ? [$tuition] : []);
        $absent = $this->getAttendanceRepository()->findAbsentByStudent($student, $tuition !== null ? [$tuition] : []);
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
            $lateAttendanceCollection,
            $absentAttendanceCollection,
            $comments
        );
    }


}