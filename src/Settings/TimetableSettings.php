<?php

namespace App\Settings;

class TimetableSettings extends AbstractSettings {

    public const MaxLessonsKey = 'max_lessons';
    public const Days = 'days';
    public const DescriptionBeforeKey = 'lesson.%d.before_label';
    public const StartKey = 'lesson.%d.start';
    public const EndKey = 'lesson.%d.end';
    public const CollapsibleKey = 'lesson.%d.collapsable';
    public const SupervisionLabelKey = 'supervision.label';
    public const SupervisionColorKey = 'supervision.color';
    public const CategoriesKey = 'no_lessons_categories';
    public const GradesWithCourseNames = 'course_names';
    public const GradesWithMembershipTypes = 'membership_types';

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    protected function getValue($key, $default = null) {
        return parent::getValue(
            sprintf('timetable.%s', $key),
            $default
        );
    }

    protected function setValue($key, $value): void {
        parent::setValue(
            sprintf('timetable.%s', $key),
            $value
        );
    }

    private function getKeyName(string $key, int $lesson): string {
        return sprintf($key, $lesson);
    }

    public function getDescriptionBeforeLesson(int $lesson): ?string {
        return $this->getValue(
            $this->getKeyName(static::DescriptionBeforeKey, $lesson)
        );
    }

    public function setDescriptionBeforeLesson(int $lesson, ?string $description): void {
        $this->setValue(
            $this->getKeyName(static::DescriptionBeforeKey, $lesson),
            $description
        );
    }

    public function getStart($lesson) {
        return $this->getValue(
            $this->getKeyName(static::StartKey, $lesson)
        );
    }

    public function getEnd($lesson) {
        return $this->getValue(
            $this->getKeyName(static::EndKey, $lesson)
        );
    }

    public function setStart($lesson, $time = null): void {
        $this->setValue(
            $this->getKeyName(static::StartKey, $lesson),
            $time
        );
    }

    public function setEnd($lesson, $time = null): void {
        $this->setValue(
            $this->getKeyName(static::EndKey, $lesson),
            $time
        );
    }

    public function setCollapsible($lesson, bool $isCollapsable): void {
        $this->setValue(
            $this->getKeyName(static::CollapsibleKey, $lesson),
            $isCollapsable
        );
    }

    public function isCollapsible($lesson): bool {
        return $this->getValue(
            $this->getKeyName(static::CollapsibleKey, $lesson),
            false
        );
    }

    /**
     * @return int[]
     */
    public function getCategoryIds(): array {
        return $this->getValue(static::CategoriesKey, [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setCategoryIds(array $ids): void {
        $this->setValue(static::CategoriesKey, $ids);
    }

    /**
     * @return int[]
     */
    public function getGradeIdsWithCourseNames(): array {
        return $this->getValue(static::GradesWithCourseNames, [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setGradeIdsWithCourseNames(array $ids): void {
        $this->setValue(static::GradesWithCourseNames, $ids);
    }

    /**
     * @return int[]
     */
    public function getGradeIdsWithMembershipTypes(): array {
        return $this->getValue(static::GradesWithMembershipTypes, [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setGradeIdsWithMembershipTypes(array $ids): void {
        $this->setValue(static::GradesWithMembershipTypes, $ids);
    }

    public function setSupervisionLabel(?string $label): void {
        $this->setValue(static::SupervisionLabelKey, $label);
    }

    public function getSupervisionLabel(): ?string {
        return $this->getValue(static::SupervisionLabelKey);
    }

    public function setSupervisionColor(?string $color): void {
        $this->setValue(static::SupervisionColorKey, $color);
    }

    public function getSupervisionColor(): ?string {
        return $this->getValue(static::SupervisionColorKey, null);
    }

    public function getMaxLessons(): int {
        return (int)$this->getValue(static::MaxLessonsKey, 0);
    }

    public function setMaxLessons(int $maxLessons): void {
        $this->setValue(static::MaxLessonsKey, $maxLessons);
    }

    public function getDays(): array {
        return $this->getValue(static::Days, [1,2,3,4,5]);
    }

    public function setDays(array $days): void {
        $this->setValue(static::Days, $days);
    }
}