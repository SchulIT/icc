<?php

namespace App\Rooms\Status;

class RoomStatusBadge {
    /** @var string */
    private $label;

    /** @var string|null */
    private $icon;

    /** @var int */
    private $counter = 0;

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @param string $label
     * @return RoomStatusBadge
     */
    public function setLabel(string $label): RoomStatusBadge {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return RoomStatusBadge
     */
    public function setIcon(?string $icon): RoomStatusBadge {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return int
     */
    public function getCounter(): int {
        return $this->counter;
    }

    /**
     * @param int $counter
     * @return RoomStatusBadge
     */
    public function setCounter(int $counter): RoomStatusBadge {
        $this->counter = $counter;
        return $this;
    }
}