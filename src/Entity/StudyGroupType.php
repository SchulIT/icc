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
}