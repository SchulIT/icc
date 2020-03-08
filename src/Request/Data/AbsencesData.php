<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AbsencesData {
    /**
     * @Serializer\Type("array<App\Request\Data\AbsenceData>")
     * @Assert\Valid()
     * @var AbsenceData[]
     */
    private $absences = [ ];

    /**
     * @return AbsenceData[]
     */
    public function getAbsences(): array {
        return $this->absences;
    }

    /**
     * @param AbsenceData[] $absences
     * @return AbsencesData
     */
    public function setAbsences(array $absences): AbsencesData {
        $this->absences = $absences;
        return $this;
    }

}