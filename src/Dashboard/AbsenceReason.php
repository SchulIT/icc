<?php

namespace App\Dashboard;

use MyCLabs\Enum\Enum;

/**
 * @method static AbsenceReason Exam()
 * @method static AbsenceReason Appointment()
 * @method static AbsenceReason Sick()
 * @method static AbsenceReason Other()
 */
class AbsenceReason extends Enum {
    public const Exam = 'exam';
    public const Appointment = 'appointment';
    public const Sick = 'sick';
    public const Other = 'other';
}