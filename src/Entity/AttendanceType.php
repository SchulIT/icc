<?php

namespace App\Entity;

enum AttendanceType: int {
    case Absent = 0;
    case Present = 1;
    case Late = 2;
}