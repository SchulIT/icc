<?php

namespace App\Untis\Html;

use MyCLabs\Enum\Enum;

/**
 * @method static HtmlAbsenceObjectiveType Teacher()
 * @method static HtmlAbsenceObjectiveType StudyGroup()
 * @method static HtmlAbsenceObjectiveType Room()
 */
class HtmlAbsenceObjectiveType extends Enum {
    public const Teacher = 'teacher';
    public const StudyGroup = 'study_group';
    public const Room = 'room';
}