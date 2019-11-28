<?php

namespace App\View\Parameter;

use App\Entity\User;
use App\Entity\UserType;

class GroupingParameter {
    public const Grades = 'grades';
    public const Teachers = 'teachers';

    public function handle(?string $grouping, User $user) :string {
        $map = [
            static::Grades => '',
            static::Teachers => ''
        ];

        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        if($isStudentOrParent) {
            return $map[static::Grades];
        }

        if($grouping === null || !array_key_exists($grouping, $map)) {
            $grouping = static::Teachers;
        }

        return $map[$grouping];
    }
}