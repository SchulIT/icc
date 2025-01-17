<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Entity\Attendance as LessonAttendanceEntity;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Utils\ArrayUtils;
use DateTime;

class AbsenceExcuseResolver extends AbstractResolver {

    /**
     * @param Tuition[] $tuitions
     */
    public function resolve(Student $student, DateTime $start, DateTime $end, array $tuitions = [ ]): StudentInfo {
        $absent = $this->getAttendanceRepository()->findAbsentByStudent($student, $start, $end, $tuitions);
        $excuseNotes = $this->getExcuseNoteRepository()->findByStudent($student);

        $excuseCollections = $this->computeExcuseCollections($excuseNotes);
        $absentAttendanceCollection = $this->computeAttendanceCollection($absent, $excuseCollections);

        return new StudentInfo($student, 0, [], $absentAttendanceCollection, [], [], []);
    }

    /**
     * @param Student[] $students
     * @param Tuition[] $tuitions
     * @return StudentInfo[]
     */
    public function resolveBulk(array $students, DateTime $start, DateTime $end, array $tuitions = [ ]): array {
        $absent = ArrayUtils::createArrayWithKeys(
            $this->getAttendanceRepository()->findAbsentByStudents($students, $start, $end, $tuitions),
            fn(LessonAttendanceEntity $a) => $a->getStudent()->getId(),
            true
        );
        $excuseNotes = ArrayUtils::createArrayWithKeys(
            $this->getExcuseNoteRepository()->findByStudents($students),
            fn(ExcuseNote $e) => $e->getStudent()->getId(),
            true
        );

        $result = [ ];

        foreach($students as $student) {
            $excuseCollections = $this->computeExcuseCollections($excuseNotes[$student->getId()] ?? [ ]);
            $absentAttendanceCollection = $this->computeAttendanceCollection($absent[$student->getId()] ?? [ ], $excuseCollections);

            $result[$student->getId()] = new StudentInfo($student, 0, [ ], $absentAttendanceCollection, [], [], []);
        }

        return $result;
    }
}