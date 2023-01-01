<?php

namespace App\Settings;

class BookSettings extends AbstractSettings {

    /**
     * @return int[]
     */
    public function getGradesGradeTeacherExcuses(): array {
        return $this->getValue('book.excuses.grades_grade_teacher_excuses', [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setGradesGradeTeacherExcuses(array $ids): void {
        $this->setValue('book.excuses.grades_grade_teacher_excuses', $ids);
    }

    /**
     * @return int[]
     */
    public function getGradesTuitionTeacherExcuses(): array {
        return $this->getValue('book.excuses.grades_tuition_teacher_excuses', [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setGradesTuitionTeacherExcuses(array $ids): void {
        $this->setValue('book.excuses.grades_tuition_teacher_excuses', $ids);
    }

    public function getExcludeStudentsStatus(): array {
        return $this->getValue('book.exclude_students_status', [ ]);
    }

    public function setExcludeStudentsStatus(array $status): void {
        $this->setValue('book.exclude_students_status', $status);
    }
}