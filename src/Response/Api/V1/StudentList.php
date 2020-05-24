<?php

namespace App\Response\Api\V1;

class StudentList {

    /**
     * @Serializer\SerializedName("students")
     * @Serializer\Type("array<App\Response\Api\V1\Student>")
     *
     * @var Student[]
     */
    private $students;

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param Student[] $students
     * @return StudentList
     */
    public function setStudents(array $students): StudentList {
        $this->students = $students;
        return $this;
    }

}