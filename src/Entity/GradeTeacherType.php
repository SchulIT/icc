<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static GradeTeacherType Primary()
 * @method static GradeTeacherType Substitute()
 */
class GradeTeacherType extends Enum {
    private const Primary = 'primary';
    private const Substitute = 'substitute';
}