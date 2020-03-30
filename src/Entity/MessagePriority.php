<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static MessagePriority Emergency()
 * @method static MessagePriority Important()
 * @method static MessagePriority Normal()
 */
class MessagePriority extends Enum {
    private const Emergency = 'emergency';
    private const Important = 'important';
    private const Normal = 'normal';
}