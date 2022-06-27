<?php

namespace App\Untis\Html\Timetable;

use MyCLabs\Enum\Enum;

/**
 * @method static CellInformationType Weeks()
 * @method static CellInformationType Subject()
 * @method static CellInformationType Teacher()
 * @method static CellInformationType Room()
 * @method static CellInformationType Grade()
 */
class CellInformationType extends Enum {
    public const Weeks = 1;
    public const Subject = 2;
    public const Teacher = 3;
    public const Room = 4;
    public const Grade = 5;
}