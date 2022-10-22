<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Substitution;

class SubstitutionGradeGroup implements GroupInterface, SortableGroupInterface {

    /** @var Substitution[] */
    private array $substitutions = [ ];

    public function __construct(private ?Grade $grade)
    {
    }

    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @return Substitution[]
     */
    public function getSubstitutions(): array {
        return $this->substitutions;
    }

    /**
     * @return Grade|null
     */
    public function getKey() {
        return $this->grade;
    }

    /**
     * @param Substitution $item
     */
    public function addItem($item) {
        $this->substitutions[] = $item;
    }

    public function &getItems(): array {
        return $this->substitutions;
    }
}