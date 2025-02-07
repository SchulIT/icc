<?php

namespace App\Settings;

class TeacherAbsenceSettings extends AbstractSettings {

    public function getOnCreateRecipients(): array {
        return $this->getValue('teacher_absences.recipients.on_create', []);
    }

    public function setOnCreateRecipients(array $recipients): void {
        $this->setValue('teacher_absences.recipients.on_create', $recipients);
    }

    public function getOnUpdateRecipients(): array {
        return $this->getValue('teacher_absences.recipients.on_update', [ ]);
    }

    public function setOnUpdateRecipients(array $recipients): void {
        $this->setValue('teacher_absences.recipients.on_update', $recipients);
    }
}