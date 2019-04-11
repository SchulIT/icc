<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static StudyGroupType Grade()
 * @method static StudyGroupType Course()
 */
class StudyGroupType extends Enum {
    private const Grade = 1;
    private const Course = 2;

    public static function castValueIn($value) {
        return (int)$value;
    }

    public static function castValueOut($value) {
        return (string)$value;
    }
}