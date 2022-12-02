<?php

namespace App\Untis\Html\Timetable;

enum CellInformationType: int {
    case Weeks = 1;
    case Subject = 2;
    case Teacher = 3;
    case Room = 4;
    case Grade = 5;
}