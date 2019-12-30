<?php

namespace App\Dashboard;

use MyCLabs\Enum\Enum;

/**
 * @method static AbsenceReason Exam()
 * @method static AbsenceReason Other()
 */
class AbsenceReason extends Enum {
    public const Exam = 'exam';
    public const Other = 'other';
}