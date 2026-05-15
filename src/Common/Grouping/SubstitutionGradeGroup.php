<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\Substitution\Entity\Substitution;

/**
 * @implements SortableGroupInterface<Grade|null, Substitution>
 */
class SubstitutionGradeGroup implements GroupInterface, SortableGroupInterface {

    /** @var Substitution[] */
    private array $substitutions = [ ];

    public function __construct(private readonly ?Grade $grade)
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

    public function getKey(): ?Grade {
        return $this->grade;
    }

    public function addItem($item): void {
        $this->substitutions[] = $item;
    }

    public function &getItems(): array {
        return $this->substitutions;
    }
}