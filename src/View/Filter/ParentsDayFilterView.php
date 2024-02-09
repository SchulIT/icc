<?php

namespace App\View\Filter;

use App\Entity\ParentsDay;

class ParentsDayFilterView {
    /**
     * @param ParentsDay[] $parentsDays
     * @param ParentsDay|null $currentParentsDay
     */
    public function __construct(private readonly array $parentsDays, private readonly ?ParentsDay $currentParentsDay) {

    }

    /**
     * @return ParentsDay|null
     */
    public function getCurrentParentsDay(): ?ParentsDay {
        return $this->currentParentsDay;
    }

    /**
     * @return ParentsDay[]
     */
    public function getParentsDays(): array {
        return $this->parentsDays;
    }
}