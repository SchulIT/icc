<?php

namespace App\Untis\Html\Timetable;

use MyCLabs\Enum\Enum;

/**
 * @method static TimetableType Grade()
 * @method static TimetableType Subject()
 */
class TimetableType extends Enum {
    public const Grade = 'grade';
    public const Subject = 'subject';
}