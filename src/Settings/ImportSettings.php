<?php

namespace App\Settings;

class ImportSettings extends AbstractSettings {
    public function getExamRules(): array {
        return $this->getValue('import.exam_write_rules', [ ]);
    }

    public function setExamRules(array $rules): void {
        $this->setValue('import.exam_write_rules', $rules);
    }
}