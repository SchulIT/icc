<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static StudentStatus New()
 * @method static StudentStatus Waiting()
 * @method static StudentStatus Active()
 * @method static StudentStatus OnLeave()
 * @method static StudentStatus External()
 * @method static StudentStatus Graduated()
 * @method static StudentStatus Leaving()
 */
class StudentStatus extends Enum {
    private const New = 0;
    private const Waiting = 1;
    private const Active = 2;
    private const OnLeave = 3;
    private const External = 5;
    private const Graduated = 8;
    private const Leaving = 9;

    public static function castValueIn($value) {
        return (int) $value;
    }
}