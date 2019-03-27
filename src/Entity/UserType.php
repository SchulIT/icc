<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * Defines all possible types of users
 *
 * @method static UserType Teacher()
 * @method static UserType Student()
 * @method static UserType Parent()
 * @method static UserType Staff()
 * @method static UserType Intern()
 */
class UserType extends Enum {
    private const Teacher = 'teacher';
    private const Student = 'student';
    private const Parent = 'parent';
    private const Staff = 'staff';
    private const Intern = 'intern';
}