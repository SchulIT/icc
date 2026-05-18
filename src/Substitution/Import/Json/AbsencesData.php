<?php

namespace App\Substitution\Import\Json;

use App\Framework\Validator\UniqueId;
use App\Framework\Import\Json\ContextTrait;
use App\Substitution\Import\Json\AbsenceData;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AbsencesData {

    use ContextTrait;

    /**
     * @var AbsenceData[]
     */
    #[Assert\Valid]
    #[Serializer\Type('array<' . AbsenceData::class . '>')]
    private array $absences = [ ];

    /**
     * @return AbsenceData[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    /**
     * @param AbsenceData[] $absences
     */
    public function setAbsences(array $absences): AbsencesData {
        $this->absences = $absences;
        return $this;
    }

}