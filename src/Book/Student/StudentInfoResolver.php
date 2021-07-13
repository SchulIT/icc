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

class StudentInfoResolver {
    private $attendanceRepository;
    private $excuseNoteRepository;
    private $commentRepository;
    private $sorter;

    public function __construct(LessonAttendanceRepositoryInterface $attendanceRepository, ExcuseNoteRepositoryInterface $excuseNoteRepository,
                                BookCommentRepositoryInterface $commentRepository, Sorter $sorter) {
        $this->attendanceRepository = $attendanceRepository;
        $this->excuseNoteRepository = $excuseNoteRepository;
        $this->commentRepository = $commentRepository;
        $this->sorter = $sorter;
    }

    public function resolveStudentInfo(Student $student, ?Section $section, ?Tuition $tuition = null) {
        $late = $this->attendanceRepository->findLateByStudent($student, $tuition);
        $absent = $this->attendanceRepository->findAbsentByStudent($student, $tuition);
        $excuseNotes = $this->excuseNoteRepository->findByStudent($student);

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

    /**
     * @param ExcuseNote[] $excuseNotes
     * @return ExcuseCollection[]
     */
    private function computeExcuseCollections(array $excuseNotes): array {
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
    private function computeAttendanceCollection(array $attendances, array $excuseCollection): array {
        $lessonAttendance = [ ];

        foreach($attendances as $attendance) {
            for($lesson = $attendance->getEntry()->getLessonStart() + ($attendance->getEntry()->getLessonEnd() - $attendance->getEntry()->getLessonStart() - $attendance->getAbsentLessons() + 1); $lesson <= $attendance->getEntry()->getLessonEnd(); $lesson++) {
                $key = sprintf('%s-%d', $attendance->getEntry()->getDate()->format('Y-m-d'), $lesson);

                $excuses = new ExcuseCollection($attendance->getEntry()->getDate(), $lesson);

                if(isset($excuseCollection[$key])) {
                    $excuses = $excuseCollection[$key];
                }

                $lessonAttendance[] = new LessonAttendance($attendance->getEntry()->getDate(), $lesson, $attendance, $excuses);
            }
        }

        return $lessonAttendance;
    }
}