<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradesData {
    /**
     * @Serializer\Type("array<App\Request\Data\GradeData>")
     * @UniqueId(propertyPath="id")
     * @var GradeData[]
     */
    #[Assert\Valid]
    private array $grades = [ ];

    /**
     * @return GradeData[]
     */
    public function getGrades() {
        return $this->grades;
    }

    /**
     * @param GradeData[] $grades
     */
    public function setGrades($grades): GradesData {
        $this->grades = $grades;
        return $this;
    }
}