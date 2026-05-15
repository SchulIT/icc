<?php

namespace App\Common\Entity;

enum IcsAccessTokenType: string {
    case Timetable = 'timetable';
    case Calendar = 'calendar';
    case Exams = 'exams';
}