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

    public function setRegularFont(string $base64font): void {
        $this->setValue('book.export.font.regular', $base64font);
    }

    public function getRegularFont(): ?string {
        return $this->getValue('book.export.font.regular', null);
    }

    public function setBoldFont(string $base64font): void {
        $this->setValue('book.export.font.bold', $base64font);
    }

    public function getBoldFont(): ?string {
        return $this->getValue('book.export.font.bold', null);
    }

    public function getExercisesDays(): int {
        return $this->getValue('book.exercises.days', 7);
    }

    public function setExercisesDays(int $homeworkDays): void {
        $this->setValue('book.exercises.days', $homeworkDays);
    }
}