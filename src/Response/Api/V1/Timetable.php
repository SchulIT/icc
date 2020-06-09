<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class Timetable {

    /**
     * @Serializer\SerializedName("period")
     * @Serializer\Type("App\Response\Api\V1\TimetablePeriod")
     * @var TimetablePeriod
     */
    private $period;

    /**
     * @Serializer\SerializedName("lessons")
     * @Serializer\Type("array<App\Response\Api\V1\TimetableLesson>")
     * @var TimetableLesson[]
     */
    private $lessons;

    /**
     * @Serializer\SerializedName("supervisions")
     * @Serializer\Type("array<App\Response\Api\V1\TimetableSupervision>")
     * @var TimetableSupervision[]
     */
    private $supervisions;

    /**
     * @return TimetablePeriod
     */
    public function getPeriod(): TimetablePeriod {
        return $this->period;
    }

    /**
     * @param TimetablePeriod $period
     * @return Timetable
     */
    public function setPeriod(TimetablePeriod $period): Timetable {
        $this->period = $period;
        return $this;
    }

    /**
     * @return TimetableLesson[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

    /**
     * @param TimetableLesson[] $lessons
     * @return Timetable
     */
    public function setLessons(array $lessons): Timetable {
        $this->lessons = $lessons;
        return $this;
    }

    /**
     * @return TimetableSupervision[]
     */
    public function getSupervisions(): array {
        return $this->supervisions;
    }

    /**
     * @param TimetableSupervision[] $supervisions
     * @return Timetable
     */
    public function setSupervisions(array $supervisions): Timetable {
        $this->supervisions = $supervisions;
        return $this;
    }
}