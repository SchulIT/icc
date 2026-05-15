<?php

namespace App\Book\Entity;

enum AttendanceExcuseStatus: int {
    case NotSet = 0;
    case Excused = 1;
    case NotExcused = 2;
}