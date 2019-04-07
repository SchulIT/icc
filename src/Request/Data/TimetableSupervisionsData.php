<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableSupervisionsData {

    /**
     * @Serializer\Type("array<App\Request\Data\TimetableSupervisionData>")
     * @Assert\Valid()
     * @var TimetableSupervisionData[]
     */
    private $supervisions;

    /**
     * @return TimetableSupervisionData[]
     */
    public function getSupervisions(): array {
        return $this->supervisions;
    }

    /**
     * @param TimetableSupervisionData[] $supervisions
     * @return TimetableSupervisionsData
     */
    public function setSupervisions(array $supervisions): TimetableSupervisionsData {
        $this->supervisions = $supervisions;
        return $this;
    }
}