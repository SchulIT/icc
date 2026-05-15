<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Common\Entity\Tuition;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<Grade, Tuition>
 */
class TuitionGradeGroup implements SortableGroupInterface {

    /** @var Tuition[] */
    private array $tuitions = [ ];

    public function __construct(private readonly Grade $grade) { }

    public function getGrade(): Grade {
        return $this->grade;
    }

    public function getKey(): Grade {
        return $this->grade;
    }

    public function addItem($item): void {
        $this->tuitions[] = $item;
    }

    public function &getItems(): array {
        return $this->tuitions;
    }
}