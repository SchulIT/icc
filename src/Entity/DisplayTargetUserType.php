<?php

namespace App\Entity;

enum DisplayTargetUserType: string {
    case Students = 'students';

    case Teachers = 'teachers';
}