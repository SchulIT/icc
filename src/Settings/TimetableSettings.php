<?php

namespace App\Settings;

class TimetableSettings extends AbstractSettings {
    const StartDate = 'start';
    const EndDate = 'end';
    const Collapsible = 'collapsible';
    const BeforeDescription = 'before.description';
    const SupervisionSubject = 'supervision.subject';

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function getKeyName($lesson, $settingsType) {
        if($lesson !== null) {
            return sprintf('timetable.%d.%s', $lesson, strtolower($settingsType));
        }

        if($settingsType === 'categories') {
            return 'timetable.categories';
        }

        if(substr($settingsType, 0, strlen('supervision')) === 'supervision') {
            return sprintf('timetable.%s', $settingsType);
        }

        throw new \InvalidArgumentException();
    }

    public function getDescriptionBeforeLesson($lesson) {
        $key = $this->getKeyName($lesson, static::BeforeDescription);
        return $this->getValue($key);
    }

    public function setDescriptionBeforeLesson($lesson, $description) {
        $key = $this->getKeyName($lesson, static::BeforeDescription);
        $this->setValue($key, $description);
    }

    public function getStart($lesson) {
        $key = $this->getKeyName($lesson, TimetableSettings::StartDate);
        return $this->getValue($key);
    }

    public function getEnd($lesson) {
        $key = $this->getKeyName($lesson, TimetableSettings::EndDate);
        return $this->getValue($key);
    }

    public function setStart($lesson, $time = null) {
        $key = $this->getKeyName($lesson, TimetableSettings::StartDate);
        $this->setValue($key, $time);
    }

    public function setEnd($lesson, $time = null) {
        $key = $this->getKeyName($lesson, TimetableSettings::EndDate);
        $this->setValue($key, $time);
    }

    public function isCollapsible(int $lesson) {
        $key = $this->getKeyName($lesson, TimetableSettings::Collapsible);
        return $this->getValue($key, false);
    }

    public function setIsCollapsible(int $lesson, bool $isCollapsible) {
        $key = $this->getKeyName($lesson, TimetableSettings::Collapsible);
        $this->setValue($key, $isCollapsible);
    }

    public function getCategoryIds() {
        $key = $this->getKeyName(null, 'categories');
        return $this->getValue($key, [ ]);
    }

    public function setSupervisionSubject($subject) {
        $key = $this->getKeyName(null, static::SupervisionSubject);
        $this->setValue($key, $subject);
    }

    public function getSupervisionSubject() {
        $key = $this->getKeyName(null, static::SupervisionSubject);
        return $this->getValue($key, null);
    }
}