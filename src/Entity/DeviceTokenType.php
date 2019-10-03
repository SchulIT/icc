<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static DeviceTokenType Timetable()
 * @method static DeviceTokenType Calendar()
 * @method static DeviceTokenType Exams()
 */
class DeviceTokenType extends Enum {
    private const Timetable = 'timetable';
    private const Calendar = 'calendar';
    private const Exams = 'exams';
}