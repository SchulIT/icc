<?php

namespace App\Grouping;

use App\Converter\UserTypeStringConverter;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;

class UserTypeAndGradeStrategy implements GroupingStrategyInterface {

    private $userTypeConverter;

    public function __construct(UserTypeStringConverter $userTypeConverter) {
        $this->userTypeConverter = $userTypeConverter;
    }

    /**
     * @param User $object
     * @return string[]|string|null
     */
    public function computeKey($object) {
        if($object->getUserType()->equals(UserType::Student()) || $object->getUserType()->equals(UserType::Parent())) {
            if($object->getStudents()->count() === 0) {
                return null;
            }

            return $object->getStudents()->map(function(Student $student) {
                return $student->getGrade();
            })->toArray();
        }

        return $this->userTypeConverter->convert($object->getUserType());
    }

    /**
     * @param string $keyA
     * @param string $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA === $keyB;
    }

    /**
     * @param string|null $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new StringGroup($key);
    }
}