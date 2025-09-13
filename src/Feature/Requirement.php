<?php

namespace App\Feature;

enum Requirement: string {
    case Any = 'any';
    case All = 'all';
}