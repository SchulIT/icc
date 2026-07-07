<?php

namespace App\Book\Settings;

use App\Framework\Settings\AbstractSettings;
use DateTime;

class TuitionGradebookSettings extends AbstractSettings {
    public function getEncryptedMasterKey(): ?string {
        return $this->getValue('tuition.gradebook.key', null);
    }

    public function setEncryptedMasterKey(?string $masterKey): void {
        $this->setValue('tuition.gradebook.key', $masterKey);
    }

    public function getTtlForSessionStorage(): int {
        return $this->getValue('tuition.gradebook.ttl_session_storage', 0);
    }

    public function setTtlForSessionStorage(int $ttl): void {
        $this->setValue('tuition.gradebook.ttl_session_storage', $ttl);
    }

    public function getDueDateForGrades(): ?DateTime {
        return $this->getValue('tuition.gradebook.due_date', null);
    }

    public function setDueDateForGrades(?DateTime $dueDate): void {
        $this->setValue('tuition.gradebook.due_date', $dueDate);
    }
}
