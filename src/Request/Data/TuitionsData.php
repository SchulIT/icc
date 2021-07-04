<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionsData {

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $year;

    /**
     * @Serializer\Type("int")
     * @var int
     */
    private $section;

    /**
     * @Serializer\Type("array<App\Request\Data\TuitionData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var TuitionData[]
     */
    private $tuitions = [ ];

    /**
     * @return TuitionData[]
     */
    public function getTuitions() {
        return $this->tuitions;
    }

    /**
     * @param TuitionData[] $tuitions
     * @return TuitionsData
     */
    public function setTuitions($tuitions): TuitionsData {
        $this->tuitions = $tuitions;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int {
        return $this->year;
    }

    /**
     * @param int $year
     * @return TuitionsData
     */
    public function setYear(int $year): TuitionsData {
        $this->year = $year;
        return $this;
    }

    /**
     * @return int
     */
    public function getSection(): int {
        return $this->section;
    }

    /**
     * @param int $section
     * @return TuitionsData
     */
    public function setSection(int $section): TuitionsData {
        $this->section = $section;
        return $this;
    }
}