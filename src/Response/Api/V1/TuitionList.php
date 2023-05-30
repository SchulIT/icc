<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class TuitionList {

    /**
     *
     * @var Tuition[]
     */
    #[Serializer\SerializedName('tuitions')]
    #[Serializer\Type('array<App\Response\Api\V1\Tuition>')]
    private ?array $tuitions = null;

    /**
     * @return Tuition[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    /**
     * @param Tuition[] $tuitions
     */
    public function setTuitions(array $tuitions): TuitionList {
        $this->tuitions = $tuitions;
        return $this;
    }
}