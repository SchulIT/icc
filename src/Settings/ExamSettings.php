<?php

namespace App\Settings;

use App\Entity\UserType;

class ExamSettings extends AbstractSettings {

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function isEnabled(UserType $userType) {
        $enabledFor = $this->getValue('exams.visibility', [ ]);

        if(in_array($userType->getValue(), $enabledFor)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the number of days students can view in the future. Detaults to 0 (unlimited days)
     *
     * @return int
     */
    public function getTimeWindowForStudents(): int {
        return (int)$this->getValue('exams.window', 0);
    }

    /**
     * Returns the number of days students can view invigilators for future exams. Detaults to 0 (unlimited days)
     *
     * @return int
     */
    public function getTimeWindowForStudentsToSeeInvigilators(): int {
        return (int)$this->getValue('exams.window.invigilators', 0);
    }
}