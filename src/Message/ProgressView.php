<?php

namespace App\Message;

class ProgressView {

    /** @var int */
    private $current;

    /** @var int */
    private $total;

    public function __construct(int $current, int $total) {
        $this->current = $current;
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getCurrent(): int {
        return $this->current;
    }

    /**
     * @return int
     */
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