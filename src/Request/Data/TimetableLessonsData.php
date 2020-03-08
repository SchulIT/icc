<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableLessonsData {

    /**
     * @Serializer\Type("array<App\Request\Data\TimetableLessonData>")
     * @Assert\Valid()
     * @var TimetableLessonData[]
     */
    private $lessons = [ ];

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