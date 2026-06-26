<?php

namespace App\Appointment\External\OpenHolidaysClient\Model;

enum HolidayType: string {
    case Public = 'Public';
    case Bank = 'Bank';
    case Optional = 'Optional';
    case School = 'School';
    case BackToSchool = 'BackToSchool';
    case EndOfLessons = 'EndOfLessons';
}
