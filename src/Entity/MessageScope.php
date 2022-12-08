<?php

namespace App\Entity;

enum MessageScope: string {
    case Dashboard = 'dashboard';
    case Substitutions = 'substitutions';
    case Exams = 'exams';
    case Appointments = 'appointments';
    case Timetable = 'timetable';
    case Messages = 'messages';
    case Lists = 'lists';
}