<?php

namespace App\Appointment\External\OpenHolidaysClient\Model;

enum TemporalScope: string {
    case FullDay = 'FullDay';
    case HalfDay = 'HalfDay';
}
