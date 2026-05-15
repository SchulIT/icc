<?php

namespace App\Message\Entity;

enum MessagePriority: string {
    case Emergency = 'emergency';
    case Important = 'important';
    case Normal = 'normal';
}