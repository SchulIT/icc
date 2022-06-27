<?php

namespace App\Settings;

use App\Entity\UserType;
use DateTime;

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
    public const TimetableStartKey = 'timetable.%s.start';
    public const TimetableEndKey = 'timetable.%s.end';

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
            $this->getKeyName(self::DescriptionBeforeKey, $lesson)
        );
    }

    public function setDescriptionBeforeLesson(int $lesson, ?string $description): void {
        $this->setValue(
            $this->getKeyName(self::DescriptionBeforeKey, $lesson),
            $description
        );
    }

    public function getStart($lesson) {
        return $this->getValue(
            $this->getKeyName(self::StartKey, $lesson)
        );
    }

    public function getEnd($lesson) {
        return $this->getValue(
            $this->getKeyName(self::EndKey, $lesson)
        );
    }

    public function setStart($lesson, $time = null): void {
        $this->setValue(
            $this->getKeyName(self::StartKey, $lesson),
            $time
        );
    }

    public function setEnd($lesson, $time = null): void {
        $this->setValue(
            $this->getKeyName(self::EndKey, $lesson),
            $time
        );
    }

    public function setCollapsible($lesson, bool $isCollapsable): void {
        $this->setValue(
            $this->getKeyName(self::CollapsibleKey, $lesson),
            $isCollapsable
        );
    }

    public function isCollapsible($lesson): bool {
        return $this->getValue(
            $this->getKeyName(self::CollapsibleKey, $lesson),
            false
        );
    }

    /**
     * @return int[]
     */
    public function getCategoryIds(): array {
        return $this->getValue(self::CategoriesKey, [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setCategoryIds(array $ids): void {
        $this->setValue(self::CategoriesKey, $ids);
    }

    /**
     * @return int[]
     */
    public function getGradeIdsWithCourseNames(): array {
        return $this->getValue(self::GradesWithCourseNames, [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setGradeIdsWithCourseNames(array $ids): void {
        $this->setValue(self::GradesWithCourseNames, $ids);
    }

    /**
     * @return int[]
     */
    public function getGradeIdsWithMembershipTypes(): array {
        return $this->getValue(self::GradesWithMembershipTypes, [ ]);
    }

    /**
     * @param int[] $ids
     */
    public function setGradeIdsWithMembershipTypes(array $ids): void {
        $this->setValue(self::GradesWithMembershipTypes, $ids);
    }

    public function setSupervisionLabel(?string $label): void {
        $this->setValue(self::SupervisionLabelKey, $label);
    }

    public function getSupervisionLabel(): ?string {
        return $this->getValue(self::SupervisionLabelKey);
    }

    public function setSupervisionColor(?string $color): void {
        $this->setValue(self::SupervisionColorKey, $color);
    }

    public function getSupervisionColor(): ?string {
        return $this->getValue(self::SupervisionColorKey, null);
    }

    public function getMaxLessons(): int {
        return (int)$this->getValue(self::MaxLessonsKey, 0);
    }

    public function setMaxLessons(int $maxLessons): void {
        $this->setValue(self::MaxLessonsKey, $maxLessons);
    }

    public function getDays(): array {
        return $this->getValue(self::Days, [1,2,3,4,5]);
    }

    public function setDays(array $days): void {
        $this->setValue(self::Days, $days);
    }

    public function getStartDate(UserType $userType): ?DateTime {
        return $this->getValue(sprintf(static::TimetableStartKey, $userType->getValue()));
    }

    public function setStartDate(UserType $userType, ?DateTime $date): void {
        $this->setValue(sprintf(static::TimetableStartKey, $userType->getValue()), $date);
    }

    public function getEndDate(UserType $userType): ?DateTime {
        return $this->getValue(sprintf(static::TimetableEndKey, $userType->getValue()));
    }

    public function setEndDate(UserType $userType, ?DateTime $date): void {
        $this->setValue(sprintf(static::TimetableEndKey, $userType->getValue()), $date);
    }


}