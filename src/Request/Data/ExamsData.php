<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamsData {

    use SuppressNotificationTrait;

    /**
     * This date controls at which date the imported exams begin. Only exams starting from this date (inclusive) will
     * be considered on import.
     *
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull
     * @var DateTime|null
     */
    private ?DateTime $startDate;

    /**
     * This date controls at which date the imported exams end. Only exams before this date (inclusive) will
     * be considered on import.
     *
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull
     * @var DateTime|null
     */
    private ?DateTime $endDate;

    /**
     * @Serializer\Type("array<App\Request\Data\ExamData>")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var ExamData[]
     */
    private $exams = [ ];

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $startDate
     * @return ExamsData
     */
    public function setStartDate(?DateTime $startDate): ExamsData {
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
     * @return ExamsData
     */
    public function setEndDate(?DateTime $endDate): ExamsData {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return ExamData[]
     */
    public function getExams() {
        return $this->exams;
    }

    /**
     * @param ExamData[] $exams
     * @return ExamsData
     */
    public function setExams($exams): ExamsData {
        $this->exams = $exams;
        return $this;
    }
}