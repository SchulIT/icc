<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class FreeLessonTimespansData {

    /**
     * @Serializer\SerializedName("free_lessons")
     * @Serializer\Type("array<App\Request\Data\FreeLessonTimespanData>")
     * @Assert\Valid()
     * @var FreeLessonTimespanData[]
     */
    private $freeLessons = [ ];

    /**
     * @return FreeLessonTimespanData[]
     */
    public function getFreeLessons(): array {
        return $this->freeLessons;
    }
}