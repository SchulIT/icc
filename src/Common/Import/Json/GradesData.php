<?php

namespace App\Common\Import\Json;

use App\Common\Import\Json\GradeData;
use App\Framework\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradesData {
    /**
     * @var GradeData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<' . GradeData::class .'>')]
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