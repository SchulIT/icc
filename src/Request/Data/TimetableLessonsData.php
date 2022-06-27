<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableLessonsData {

    /**
     * This date controls at which date the imported timetable lessons begin. All existing entries starting this date
     * will be removed from the system and replaced by the ones provided by this import.
     *
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull
     * @var DateTime|null
     */
    private ?DateTime $startDate;

    /**
     * This date controls at which date the imported timetable lessons begin. All existing entries starting this date
     * will be removed from the system and replaced by the ones provided by this import.
     *
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull
     * @var DateTime|null
     */
    private ?DateTime $endDate;

    /**
     * @Serializer\Type("array<App\Request\Data\TimetableLessonData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var TimetableLessonData[]
     */
    private array $lessons = [ ];

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $startDate
     * @return TimetableLessonsData
     */
    public function setStartDate(?DateTime $startDate): TimetableLessonsData {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     * @return TimetableLessonsData
     */
    public function setEndDate(?DateTime $endDate): TimetableLessonsData {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return TimetableLessonData[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }

    /**
     * @param TimetableLessonData[] $lessons
     * @return TimetableLessonsData
     */
    public function setLessons(array $lessons): TimetableLessonsData {
        $this->lessons = $lessons;
        return $this;
    }

}