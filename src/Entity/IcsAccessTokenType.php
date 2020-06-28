<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static IcsAccessTokenType Timetable()
 * @method static IcsAccessTokenType Calendar()
 * @method static IcsAccessTokenType Exams()
 */
class IcsAccessTokenType extends Enum {
    private const Timetable = 'timetable';
    private const Calendar = 'calendar';
    private const Exams = 'exams';
}