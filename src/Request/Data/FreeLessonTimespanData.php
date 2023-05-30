<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class FreeLessonTimespanData {

    #[Assert\NotNull]
    #[Serializer\SerializedName('date')]
    #[Serializer\Type("DateTime<'Y-m-d\\TH:i:s'>")]
    private ?DateTime $date = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\NotNull]
    #[Serializer\SerializedName('start')]
    #[Serializer\Type('int')]
    private ?int $start = null;

    #[Assert\GreaterThanOrEqual(propertyPath: 'start')]
    #[Assert\NotNull]
    #[Serializer\SerializedName('end')]
    #[Serializer\Type('int')]
    private ?int $end = null;

    public function setDate(?DateTime $date): FreeLessonTimespanData {
        $this->date = $date;
        return $this;
    }

    public function setStart(?int $start): FreeLessonTimespanData {
        $this->start = $start;
        return $this;
    }

    public function setEnd(?int $end): FreeLessonTimespanData {
        $this->end = $end;
        return $this;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function getStart(): ?int {
        return $this->start;
    }

    public function getEnd(): ?int {
        return $this->end;
    }
}