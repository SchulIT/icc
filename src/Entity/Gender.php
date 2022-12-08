<?php

namespace App\Entity;

enum Gender: string {
    case Male = 'male';
    case Female = 'female';
    case X = 'x';
}