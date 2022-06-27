<?php

namespace App\Response\Api\V1;

class TeacherList {

    /**
     * @Serializer\SerializedName("teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     *
     * @var Teacher[]
     */
    private $teachers;

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param Teacher[] $teachers
     * @return TeacherList
     */
    public function setTeachers(array $teachers): TeacherList {
        $this->teachers = $teachers;
        return $this;
    }
}