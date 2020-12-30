<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static DisplayTargetUserType Students()
 * @method static DisplayTargetUserType Teachers()
 */
class DisplayTargetUserType extends Enum {
    private const Students = 'students';

    private const Teachers = 'teachers';
}