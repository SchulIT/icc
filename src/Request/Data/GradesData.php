<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradesData {

    /**
     * @Serializer\Type("array<App\Request\Data\GradeData>")
     * @Assert\Valid()
     * @var GradeData[]
     */
    private $grades;

    /**
     * @return GradeData[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @param GradeData[] $grades
     * @return GradesData
     */
    public function setGrades(array $grades): GradesData {
        $this->grades = $grades;
        return $this;
    }
}