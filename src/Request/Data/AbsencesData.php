<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AbsencesData {

    use ContextTrait;

    /**
     * @Serializer\Type("array<App\Request\Data\AbsenceData>")
     * @var AbsenceData[]
     */
    #[Assert\Valid]
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