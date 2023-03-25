<?php

namespace App\Settings;

class TuitionGradebookSettings extends AbstractSettings {
    public function getEncryptedMasterKey(): ?string {
        return $this->getValue('tuition.gradebook.key', null);
    }

    public function setEncryptedMasterKey(?string $masterKey): void {
        $this->setValue('tuition.gradebook.key', $masterKey);
    }
}