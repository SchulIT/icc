<?php

namespace App\Entity;

enum IcsAccessTokenType: string {
    case Timetable = 'timetable';
    case Calendar = 'calendar';
    case Exams = 'exams';
}