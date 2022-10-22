<?php

namespace App\Rooms\Status;

class RoomStatusBadge {
    private ?string $label = null;

    private ?string $icon = null;

    private int $counter = 0;

    public function getLabel(): string {
        return $this->label;
    }

    public function setLabel(string $label): RoomStatusBadge {
        $this->label = $label;
        return $this;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): RoomStatusBadge {
        $this->icon = $icon;
        return $this;
    }

    public function getCounter(): int {
        return $this->counter;
    }

    public function setCounter(int $counter): RoomStatusBadge {
        $this->counter = $counter;
        return $this;
    }
}