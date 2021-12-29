<?php

namespace App\Book\Student;

use App\Entity\Student;
use App\Entity\Tuition;

class AbsenceExcuseResolver extends AbstractResolver {

    /**
     * @param Student $student
     * @param Tuition[] $tuitions
     */
    public function resolve(Student $student, array $tuitions = [ ]) {
        $absent = $this->getAttendanceRepository()->findAbsentByStudent($student, $tuitions);
        $excuseNotes = $this->getExcuseNoteRepository()->findByStudent($student);

        $excuseCollections = $this->computeExcuseCollections($excuseNotes);
        $absentAttendanceCollection = $this->computeAttendanceCollection($absent, $excuseCollections);

        return new StudentInfo($student, 0, [], $absentAttendanceCollection, []);
    }
}