<?php

namespace App\Book\AttendanceSuggestion;

use App\Common\Entity\Student as StudentEntity;
use App\Book\Xhr\Response\Student;

trait StudentTransformerTrait {
    private function getStudent(StudentEntity $entity): Student {
        return new Student($entity->getUuid()->toString(), $entity->getFirstname(), $entity->getLastname());
    }
}