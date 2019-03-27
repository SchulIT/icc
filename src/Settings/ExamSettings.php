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
}