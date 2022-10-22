<?php

namespace App\Grouping;

use App\Entity\Substitution;
use App\Entity\Teacher;

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