<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableSupervisionsData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $period;

    /**
     * @Serializer\Type("array<App\Request\Data\TimetableSupervisionData>")
     * @Assert\Valid()
     * @var TimetableSupervisionData[]
     */
    private $supervisions = [ ];

    /**
     * @return string|null
     */
    public function getPeriod(): ?string {
        return $this->period;
    }

    /**
     * @param string|null $period
     * @return TimetableSupervisionsData
     */
    public function setPeriod(?string $period): TimetableSupervisionsData {
        $this->period = $period;
        return $this;
    }

    /**
     * @return TimetableSupervisionData[]
     */
    public function getSupervisions() {
        return $this->supervisions;
    }

    /**
     * @param TimetableSupervisionData[] $supervisions
     * @return TimetableSupervisionsData
     */
    public function setSupervisions($supervisions): TimetableSupervisionsData {
        $this->supervisions = $supervisions;
        return $this;
    }
}