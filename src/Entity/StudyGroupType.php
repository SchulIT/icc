<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static StudyGroupType Grade()
 * @method static StudyGroupType Course()
 */
class StudyGroupType extends Enum {
    private const Grade = "grade";
    private const Course = "course";
}