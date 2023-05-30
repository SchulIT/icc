<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Week {

    #[Serializer\Type('integer')]
    #[Serializer\SerializedName('week_number')]
    private int $weekNumber = 0;

    #[Serializer\Type('DateTime')]
    #[Serializer\SerializedName('start')]
    private ?DateTime $start = null;

    #[Serializer\Type('DateTime')]
    #[Serializer\SerializedName('end')]
    private ?DateTime $end = null;

    /**
     * @var Day[]
     */
    #[Serializer\Type('array<App\Book\Export\Day>')]
    #[Serializer\SerializedName('days')]
    private ?array $days = null;

    public function getWeekNumber(): int {
        return $this->weekNumber;
    }

    public function setWeekNumber(int $weekNumber): Week {
        $this->weekNumber = $weekNumber;
        return $this;
    }

    public function getStart(): DateTime {
        return $this->start;
    }

    public function setStart(DateTime $start): Week {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): DateTime {
        return $this->end;
    }

    public function setEnd(DateTime $end): Week {
        $this->end = $end;
        return $this;
    }

    public function addDay(Day $day): void {
        $this->days[] = $day;
    }

    /**
     * @return Day[]
     */
    public function getDays(): array {
        return $this->days;
    }
}