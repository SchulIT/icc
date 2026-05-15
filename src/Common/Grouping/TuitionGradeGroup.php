<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Common\Entity\Tuition;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

class TuitionGradeGroup implements GroupInterface, SortableGroupInterface {

    private readonly Grade $grade;

    /** @var Tuition[] */
    private array $tuitions = [ ];

    public function __construct(Grade $grade) {
        $this->grade = $grade;
    }

    public function getGrade(): Grade {
        return $this->grade;
    }

    public function getKey(): Grade {
        return $this->grade;
    }

    public function addItem($item) {
        $this->tuitions[] = $item;
    }

    public function &getItems(): array {
        return $this->tuitions;
    }
}