<?php

namespace App\Settings;

class TimetableSettings extends AbstractSettings {

    private const MaxLessonsKey = 'max_lessons';
    private const DescriptionBeforeKey = 'lesson.%d.before_label';
    private const StartKey = 'lesson.%d.start';
    private const EndKey = 'lesson.%d.end';
    private const CollapsibleKey = 'lesson.%d.collapsable';
    private const SupervisionLabelKey = 'supervision.label';
    private const CategoriesKey = 'no_lessons_categories';

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

    public function setSupervisionLabel(?string $label): void {
        $this->setValue(static::SupervisionLabelKey, $label);
    }

    public function getSupervisionLabel(): ?string {
        return $this->getValue(static::SupervisionLabelKey);
    }

    public function getMaxLessons(): int {
        return (int)$this->getValue(static::MaxLessonsKey, 0);
    }

    public function setMaxLessons(int $maxLessons): void {
        $this->setValue(static::MaxLessonsKey, $maxLessons);
    }
}