<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradesData {

    /**
     * @Serializer\Type("array<App\Request\Data\GradeData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var GradeData[]
     */
    private $grades = [ ];

    /**
     * @return GradeData[]
     */
    public function getGrades() {
        return $this->grades;
    }

    /**
     * @param GradeData[] $grades
     * @return GradesData
     */
    public function setGrades($grades): GradesData {
        $this->grades = $grades;
        return $this;
    }
}