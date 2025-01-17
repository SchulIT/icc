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

    public function isAttendanceVisibleForStudentsAndParentsEnabled(): bool {
        return $this->getValue('book.attendance.visible_for_students_and_parents', false);
    }

    public function setAttendanceVisibleForStudentsAndParentsEnabled(bool $isEnabled): void {
        $this->setValue('book.attendance.visible_for_students_and_parents', $isEnabled);
    }

    public function isLessonTopicsVisibleForStudentsAndParentsEnabled(): bool {
        return $this->getValue('book.lesson_topics.visible_for_students_and_parents', false);
    }

    public function setLessonTopicsVisibleForStudentsAndParentsEnabled(bool $isEnabled): void {
        $this->setValue('book.lesson_topics.visible_for_students_and_parents', $isEnabled);
    }

    public function isIntegrityCheckEnabled(string $check): bool {
        return $this->getValue(sprintf('book.integrity_check.%s.enabled', $check), true);
    }

    public function setIntegrityCheckEnabled(string $check, bool $isEnabled): void {
        $this->setValue(sprintf('book.integrity_check.%s.enabled', $check), $isEnabled);
    }

    public function getSuggestionPriorityForAbsenceType(string $uuid): int {
        return $this->getValue('book.attendance_suggestion.priority.' . $uuid, 100);
    }

    public function setSuggestionPriorityForAbsenceType(string $uuid, int $priority): void {
        $this->setValue('book.attendance_suggestion.priority.' . $uuid, $priority);
    }

    public function getSuggestionPriorityForExams(): int {
        return $this->getValue('book.attendance_suggestion.priority.exam', 60);
    }

    public function setSuggestionPriorityForExams(int $priority): void {
        $this->setValue('book.attendance_suggestion.priority.exam', $priority);
    }

    public function getSuggestionPriorityForPreviouslyAbsent(): int {
        return $this->getValue('book.attendance_suggestion.priority.previously_absent', 20);
    }

    public function setSuggestionPriorityForPreviouslyAbsent(int $priority): void {
        $this->setValue('book.attendance_suggestion.priority.previously_absent', $priority);
    }

    public function getSuggestionPriorityForExcuseNote(): int {
        return $this->getValue('book.attendance_suggestion.priority.excuse_note', 40);
    }

    public function setSuggestionPriorityForExcuseNote(int $priority): void {
        $this->setValue('book.attendance_suggestion.priority.excuse_note', $priority);
    }

    public function getSuggestionPriorityForBookEvent(): int {
        return $this->getValue('book.attendance_suggestion.priority.book_event', 120);
    }

    public function setSuggestionPriorityForBookEvent(int $priority): void {
        $this->setValue('book.attendance_suggestion.priority.book_event', $priority);
    }

    public function getSuggestionPriorityForAbsentStudyGroup(): int {
        return $this->getValue('book.attendance_suggestion.priority.absent_study_group', 70);
    }

    public function setSuggestionPriorityForAbsentStudyGroup(int $priority): void {
        $this->setValue('book.attendance_suggestion.priority.absent_study_group', $priority);
    }

    public function getSuggestionPriorityForRemoval(): int {
        return $this->getValue('book.attendance_suggestion.priority.removal', 20);
    }

    public function setSuggestionPriorityForRemoval(int $priority): void {
        $this->setValue('book.attendance_suggestion.priority.removal', $priority);
    }

    public function setNotifyParentsOnStudentAbsenceWithoutSuggestion(bool $notifiy): void {
        $this->setValue('book.attendance.notify_parents_on_absence_without_suggestion', $notifiy);
    }

    public function getNotifyParentsOnStudentAbsenceWithoutSuggestion(): bool {
        return $this->getValue('book.attendance.notify_parents_on_absence_without_suggestion', false);
    }

    public function setNotifyGradeTeachersOnStudentAbsenceWithoutSuggestion(bool $notify): void {
        $this->setValue('book.attendance.notify_grade_teachers_on_absence_without_suggestion', $notify);
    }

    public function getNotifyGradeTeachersOnStudentAbsenceWithoutSuggestion(): bool {
        return $this->getValue('book.attendance.notify_grade_teachers_on_absence_without_suggestion', false);
    }
}