<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * Defines genders
 *
 * @method static Gender Male()
 * @method static Gender Female()
 * @method static Gender X()
 */
class Gender extends Enum {
    private const Male = 'male';
    private const Female = 'female';
    private const X = 'x';
}