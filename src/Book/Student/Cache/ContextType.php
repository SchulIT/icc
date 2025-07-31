<?php

namespace App\Book\Student\Cache;

enum ContextType: string {
    case Teacher = 'teacher';
    case Grade = 'grade';
    case Tuition = 'tuition';
}