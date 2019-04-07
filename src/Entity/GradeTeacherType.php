<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static GradeTeacherType Primary()
 * @method static GradeTeacherType Substitutional()
 */
class GradeTeacherType extends Enum {
    private const Primary = 'p';
    private const Substitutional = 's';
}