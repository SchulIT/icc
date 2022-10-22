<?php

namespace App\Response\Api\V1;

class TeacherList {

    /**
     * @Serializer\SerializedName("teachers")
     * @Serializer\Type("array<App\Response\Api\V1\Teacher>")
     *
     * @var Teacher[]
     */
    private ?array $teachers = null;

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param Teacher[] $teachers
     */
    public function setTeachers(array $teachers): TeacherList {
        $this->teachers = $teachers;
        return $this;
    }
}