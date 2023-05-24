<?php

namespace App\Settings;

class ImportSettings extends AbstractSettings {
    public function getExamRules(): array {
        return $this->getValue('import.exam_write_rules', [ ]);
    }

    public function setExamRules(array $rules): void {
        $this->setValue('import.exam_write_rules', $rules);
    }

    public function getFallbackSection(): ?int {
        return $this->getValue('import.fallback_section');
    }

    public function setFallbackSection(?int $sectionId): void {
        $this->setValue('import.fallback_section', $sectionId);
    }

    public function getSubjectsWithoutTuition(): array {
        return $this->getValue('import.subjects_without_tuition', [ ]);
    }

    public function setSubjectsWithoutTuition(array $subjects): void {
        $this->setValue('import.subjects_without_tuition', $subjects);
    }
}