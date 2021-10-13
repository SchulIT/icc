<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static SickNoteReason Quarantine()
 * @method static SickNoteReason Sick()
 */
class SickNoteReason extends Enum {
    public const Quarantine = 'quarantine';
    public const Sick = 'sick';
}