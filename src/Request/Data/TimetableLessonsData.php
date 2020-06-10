<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableLessonsData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @UniqueId(propertyPath="id")
     * @var string|null
     */
    private $period;

    /**
     * @Serializer\Type("array<App\Request\Data\TimetableLessonData>")
     * @Assert\Valid()
     * @var TimetableLessonData[]
     */
    private $lessons = [ ];

    /**
     * @return string|null
     */
    public function getPeriod(): ?string {
        return $this->period;
    }

    /**
     * @param string|null $period
     * @return TimetableLessonsData
     */
    public function setPeriod(?string $period): TimetableLessonsData {
        $this->period = $period;
        return $this;
    }

    /**
     * @return TimetableLessonData[]
     */
    public function getLessons() {
        return $this->lessons;
    }

    /**
     * @param TimetableLessonData[] $lessons
     * @return TimetableLessonsData
     */
    public function setLessons($lessons): TimetableLessonsData {
        $this->lessons = $lessons;
        return $this;
    }

}