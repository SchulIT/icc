<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class TeacherList {

    /**
     *
     * @var Teacher[]
     */
    #[Serializer\SerializedName('teachers')]
    #[Serializer\Type('array<App\Response\Api\V1\Teacher>')]
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