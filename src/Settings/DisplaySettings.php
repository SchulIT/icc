<?php

namespace App\Settings;

class DisplaySettings extends AbstractSettings {
    public function getAllowedIpAddresses(): ?string {
        return $this->getValue('display.allowed_ips');
    }

    public function setAllowedIpAddresses(?string $addresses): void {
        $this->setValue('display.allowed_ips', $addresses);
    }
}