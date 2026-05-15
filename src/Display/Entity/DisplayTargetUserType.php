<?php

namespace App\Display\Entity;

enum DisplayTargetUserType: string {
    case Students = 'students';

    case Teachers = 'teachers';
}