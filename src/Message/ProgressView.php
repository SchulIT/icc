<?php

namespace App\Message;

class ProgressView {

    public function __construct(private int $current, private int $total)
    {
    }

    public function getCurrent(): int {
        return $this->current;
    }

    public function getTotal(): int {
        return $this->total;
    }

    public function getPercentage(): float {
        if($this->total === 0) {
            return 0;
        }

        return round((float)$this->getCurrent() / $this->getTotal() * 100);
    }
}