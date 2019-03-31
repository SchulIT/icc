<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionsData {

    /**
     * @Serializer\Type("array<App\Request\Data\TuitionData>")
     * @Assert\Valid()
     * @var TuitionData[]
     */
    private $tuitions;

    /**
     * @return TuitionData[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    /**
     * @param TuitionData[] $tuitions
     * @return TuitionsData
     */
    public function setTuitions(array $tuitions): TuitionsData {
        $this->tuitions = $tuitions;
        return $this;
    }

}