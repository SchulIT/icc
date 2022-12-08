<?php

namespace App\Entity;

enum StudyGroupType: string {
    case Grade = 'grade';
    case Course = 'course';
}