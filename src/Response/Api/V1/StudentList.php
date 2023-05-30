<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class StudentList {

    /**
     *
     * @var Student[]
     */
    #[Serializer\SerializedName('students')]
    #[Serializer\Type('array<App\Response\Api\V1\Student>')]
    private ?array $students = null;

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param Student[] $students
     */
    public function setStudents(array $students): StudentList {
        $this->students = $students;
        return $this;
    }

}