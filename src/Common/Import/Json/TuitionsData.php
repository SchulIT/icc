<?php

namespace App\Common\Import\Json;

use App\Common\Import\Json\TuitionData;
use App\Framework\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionsData {

    #[Serializer\Type('int')]
    private ?int $year = null;

    #[Serializer\Type('int')]
    private ?int $section = null;

    /**
     * @var TuitionData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<' . TuitionData::class . '>')]
    private array $tuitions = [ ];

    /**
     * @return TuitionData[]
     */
    public function getTuitions() {
        return $this->tuitions;
    }

    /**
     * @param TuitionData[] $tuitions
     */
    public function setTuitions($tuitions): TuitionsData {
        $this->tuitions = $tuitions;
        return $this;
    }

    public function getYear(): int {
        return $this->year;
    }

    public function setYear(int $year): TuitionsData {
        $this->year = $year;
        return $this;
    }

    public function getSection(): int {
        return $this->section;
    }

    public function setSection(int $section): TuitionsData {
        $this->section = $section;
        return $this;
    }
}