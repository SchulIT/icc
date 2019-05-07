<?php

namespace App\Settings;

use App\Entity\UserType;

class ExamSettings extends AbstractSettings {

    public function __construct(SettingsManager $manager) {
        parent::__construct($manager);
    }

    public function getVisibility() {
        return $this->getValue('exams.visibility', [ ]);
    }

    public function setVisibility(array $visibility) {
        $this->setValue('exams.visibility', $visibility);
    }

    /**
     * @param UserType $userType
     * @return bool
     * @deprecated
     */
    public function isEnabled(UserType $userType) {
        if(in_array($userType->getValue(), $this->getVisibility())) {
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

    public function setTimeWindowForStudents(int $timeWindow) {
        $this->setValue('exams.window', $timeWindow);
    }

    /**
     * Returns the number of days students can view invigilators for future exams. Detaults to 0 (unlimited days)
     *
     * @return int
     */
    public function getTimeWindowForStudentsToSeeInvigilators(): int {
        return (int)$this->getValue('exams.window.invigilators', 0);
    }

    public function setTimeWindowForStudentsToSeeInvigilators(int $timeWindow) {
        $this->setValue('exams.window.invigilators', $timeWindow);
    }
}