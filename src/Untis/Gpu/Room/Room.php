<?php

namespace App\Untis\Gpu\Room;

class Room {

    private string $shortName;

    private ?string $longName;

    private ?int $capacity;

    /**
     * @return string
     */
    public function getShortName(): string {
        return $this->shortName;
    }

    /**
     * @param string $shortName
     * @return Room
     */
    public function setShortName(string $shortName): Room {
        $this->shortName = $shortName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLongName(): ?string {
        return $this->longName;
    }

    /**
     * @param string|null $longName
     * @return Room
     */
    public function setLongName(?string $longName): Room {
        $this->longName = $longName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCapacity(): ?int {
        return $this->capacity;
    }

    /**
     * @param int|null $capacity
     * @return Room
     */
    public function setCapacity(?int $capacity): Room {
        $this->capacity = $capacity;
        return $this;
    }
}