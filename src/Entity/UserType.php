<?php

namespace App\Entity;

enum UserType: string {
    case Teacher = 'teacher';
    case Student = 'student';
    case Parent = 'parent';
    case Staff = 'staff';
    case Intern = 'intern';
    case User = 'user';
}