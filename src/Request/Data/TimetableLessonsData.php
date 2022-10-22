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
     * @var DateTime|null
     */
    #[Assert\NotNull]
    private ?DateTime $startDate = null;

    /**
     * This date controls at which date the imported timetable lessons begin. All existing entries starting this date
     * will be removed from the system and replaced by the ones provided by this import.
     *
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @var DateTime|null
     */
    #[Assert\NotNull]
    private ?DateTime $endDate = null;

    /**
     * @Serializer\Type("array<App\Request\Data\TimetableLessonData>")
     * @UniqueId(propertyPath="id")
     * @var TimetableLessonData[]
     */
    #[Assert\Valid]
    private array $lessons = [ ];

    public function getStartDate(): ?DateTime {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): TimetableLessonsData {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTime {
        return $this->endDate;
    }

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
     */
    public function setLessons(array $lessons): TimetableLessonsData {
        $this->lessons = $lessons;
        return $this;
    }

}