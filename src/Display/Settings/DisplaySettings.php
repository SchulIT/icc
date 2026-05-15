<?php

namespace App\Display\Settings;

use App\Framework\Settings\AbstractSettings;

class DisplaySettings extends AbstractSettings {
    public function getAllowedIpAddresses(): ?string {
        return $this->getValue('display.allowed_ips');
    }

    public function setAllowedIpAddresses(?string $addresses): void {
        $this->setValue('display.allowed_ips', $addresses);
    }
}