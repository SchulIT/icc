<?php

namespace App\Substitution\Import\Json;

use App\Framework\Import\Json\ContextTrait;
use App\Substitution\Import\Json\FreeLessonTimespanData;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class FreeLessonTimespansData {

    use ContextTrait;

    /**
     * @var FreeLessonTimespanData[]
     */
    #[Assert\Valid]
    #[Serializer\SerializedName('free_lessons')]
    #[Serializer\Type('array<' . FreeLessonTimespanData::class .'>')]
    private array $freeLessons = [ ];

    /**
     * @return FreeLessonTimespanData[]
     */
    public function getFreeLessons(): array {
        return $this->freeLessons;
    }

    /**
     * @param FreeLessonTimespanData[] $freeLessons
     */
    public function setFreeLessons(array $freeLessons): FreeLessonTimespansData {
        $this->freeLessons = $freeLessons;
        return $this;
    }
}