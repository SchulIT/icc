<?php

namespace App\Dashboard;

enum AbsenceReason: string {
    case Exam = 'exam';
    case Absence = 'absence';
    case Other = 'other';
}