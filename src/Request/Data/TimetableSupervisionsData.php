<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TimetableSupervisionsData {

    /**
     * This date controls at which date the imported timetable supervisions begin. All existing entries starting this date
     * will be removed from the system and replaced by the ones provided by this import.
     *
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull
     * @var DateTime|null
     */
    private ?DateTime $startDate;

    /**
     * This date controls at which date the imported timetable supervisions ends. All existing entries before (and including) this date
     * will be removed from the system and replaced by the ones provided by this import.
     *
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull
     * @var DateTime|null
     */
    private ?DateTime $endDate;

    /**
     * @Serializer\Type("array<App\Request\Data\TimetableSupervisionData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var TimetableSupervisionData[]
     */
    private array $supervisions = [ ];

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $startDate
     * @return TimetableSupervisionsData
     */
    public function setStartDate(?DateTime $startDate): TimetableSupervisionsData {
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
     */
    public function setEndDate(?DateTime $endDate): void {
        $this->endDate = $endDate;
    }

    /**
     * @return TimetableSupervisionData[]
     */
    public function getSupervisions(): array {
        return $this->supervisions;
    }

    /**
     * @param TimetableSupervisionData[] $supervisions
     * @return TimetableSupervisionsData
     */
    public function setSupervisions(array $supervisions): TimetableSupervisionsData {
        $this->supervisions = $supervisions;
        return $this;
    }
}