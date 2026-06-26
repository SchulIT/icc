<?php

namespace App\Appointment\External\OpenHolidaysClient\Model;

enum RegionalScope: string {
    case National = 'National';
    case Regional = 'Regional';
    case Local = 'Local';
}
