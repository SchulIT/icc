<?php

namespace App\Book\AttendanceSuggestion;

use App\Entity\Student as StudentEntity;
use App\Response\Book\Student;

trait StudentTransformerTrait {
    private function getStudent(StudentEntity $entity): Student {
        return new Student($entity->getUuid()->toString(), $entity->getFirstname(), $entity->getLastname());
    }
}