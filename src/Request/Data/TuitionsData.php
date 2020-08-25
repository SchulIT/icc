<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionsData {

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

}