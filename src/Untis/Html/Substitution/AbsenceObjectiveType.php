<?php

namespace App\Untis\Html\Substitution;

use MyCLabs\Enum\Enum;

/**
 * @method static AbsenceObjectiveType Teacher()
 * @method static AbsenceObjectiveType StudyGroup()
 * @method static AbsenceObjectiveType Room()
 */
class AbsenceObjectiveType extends Enum {
    public const Teacher = 'teacher';
    public const StudyGroup = 'study_group';
    public const Room = 'room';
}