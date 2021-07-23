<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Grade {

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     * @var string
     */
    private $name;

    /**
     * @Serializer\Type("array<App\Book\Export\Teacher>")
     * @Serializer\SerializedName("teachers")
     * @var Teacher[]
     */
    private $teachers;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Grade
     */
    public function setName(string $name): Grade {
        $this->name = $name;
        return $this;
    }

    public function addTeacher(Teacher $teacher): void {
        $this->teachers[] = $teacher;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }
}