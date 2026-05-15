<?php

namespace App\Substitution\Grouping;

use App\Common\Entity\Teacher;
use App\Framework\Grouping\SortableGroupInterface;
use App\Substitution\Entity\Substitution;

/**
 * @implements SortableGroupInterface<Teacher|null, Substitution>
 */
class SubstitutionTeacherGroup implements SortableGroupInterface {

    /** @var Substitution[] */
    private array $substitutions = [ ];

    public function __construct(private readonly ?Teacher $teacher)
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

    public function getKey(): ?Teacher {
        return $this->teacher;
    }

    public function addItem($item): void {
        $this->substitutions[] = $item;
    }

    public function &getItems(): array {
        return $this->substitutions;
    }
}