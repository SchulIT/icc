<?php

namespace App\Book\Export;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class Week {

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("week_number")
     * @var int
     */
    private $weekNumber = 0;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("start")
     * @var DateTime
     */
    private $start;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\SerializedName("end")
     * @var DateTime
     */
    private $end;

    /**
     * @Serializer\Type("array<App\Book\Export\Day>")
     * @Serializer\SerializedName("days")
     * @var Day[]
     */
    private $days;

    /**
     * @return int
     */
    public function getWeekNumber(): int {
        return $this->weekNumber;
    }

    /**
     * @param int $weekNumber
     * @return Week
     */
    public function setWeekNumber(int $weekNumber): Week {
        $this->weekNumber = $weekNumber;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime {
        return $this->start;
    }

    /**
     * @param DateTime $start
     * @return Week
     */
    public function setStart(DateTime $start): Week {
        $this->start = $start;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime {
        return $this->end;
    }

    /**
     * @param DateTime $end
     * @return Week
     */
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