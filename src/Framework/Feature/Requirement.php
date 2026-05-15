<?php

namespace App\Framework\Feature;

enum Requirement: string {
    case Any = 'any';
    case All = 'all';
}