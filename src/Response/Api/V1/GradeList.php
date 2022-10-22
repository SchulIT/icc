<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class GradeList {

    /**
     * @Serializer\SerializedName("grades")
     * @Serializer\Type("array<App\Response\Api\V1\Grade>")
     *
     * @var Grade[]
     */
    private ?array $grades = null;

    /**
     * @return Grade[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @param Grade[] $grades
     */
    public function setGrades(array $grades): GradeList {
        $this->grades = $grades;
        return $this;
    }
}