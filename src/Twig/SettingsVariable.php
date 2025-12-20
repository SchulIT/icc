<?php

namespace App\Twig;

use App\Settings\DashboardSettings;

readonly class SettingsVariable {
    public function __construct(
        public DashboardSettings $dashboard
    ) { }
}
