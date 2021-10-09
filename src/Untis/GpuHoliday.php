<?php

namespace App\Untis;

use DateTime;

class GpuHoliday {

    /** @var string */
    private $shortName;

    /** @var string */
    private $longName;

    /** @var DateTime */
    private $from;

    /** @var DateTime */
    private $to;

    /** @var bool  */
    private $isHoliday = false;

    /**
     * @return string
     */
    public function getShortName(): string {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     * @return GpuHoliday
     */
    public function setShortName(string $shortName): GpuHoliday {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongName(): string {
        return $this->longName;
    }

    /**
     * @param string $longName
     * @return GpuHoliday
     */
    public function setLongName(string $longName): GpuHoliday {
        $this->longName = $longName;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFrom(): DateTime {
        return $this->from;
    }

    /**
     * @param DateTime $from
     * @return GpuHoliday
     */
    public function setFrom(DateTime $from): GpuHoliday {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTo(): DateTime {
        return $this->to;
    }

    /**
     * @param DateTime $to
     * @return GpuHoliday
     */
    public function setTo(DateTime $to): GpuHoliday {
        $this->to = $to;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHoliday(): bool {
        return $this->isHoliday;
    }

    /**
     * @param bool $isHoliday
     * @return GpuHoliday
     */
    public function setIsHoliday(bool $isHoliday): GpuHoliday {
        $this->isHoliday = $isHoliday;
        return $this;
    }
}