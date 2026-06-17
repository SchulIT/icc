<?php

namespace App\Book\Settings;

use App\Framework\Settings\AbstractSettings;

class ReportRemarksSettings extends AbstractSettings {
    public function getInformationText(): ?string {
        return $this->getValue('book.report_remarks.info', null);
    }

    public function setInformationText(?string $value): void {
        $this->setValue('book.report_remarks.info', $value);
    }
}
