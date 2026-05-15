<?php

namespace App\Substitution\Grouping;

use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;
use App\Substitution\Entity\Substitution;
use App\Common\Entity\Teacher;

class SubstitutionTeacherGroup implements GroupInterface, SortableGroupInterface {

    /** @var Substitution[] */
    private array $substitutions = [ ];

    public function __construct(private ?Teacher $teacher)
    {
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @return Substitution[]
     */
    public function getSubstitutions(): array {
        return $this->substitutions;
    }

    /**
     * @return Teacher|null
     */
    public function getKey() {
        return $this->teacher;
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