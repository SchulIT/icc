<?php

namespace App\Common\Grouping;

use App\Common\Entity\Grade;
use App\Common\Entity\StudyGroup;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<Grade, StudyGroup>
 */
class StudyGroupGradeGroup implements SortableGroupInterface {

    /** @var StudyGroup[] */
    private array $studyGroups = [ ];

    public function __construct(private readonly Grade $grade)
    {
    }

    public function getGrade(): Grade {
        return $this->grade;
    }

    /**
     * @return StudyGroup[]
     */
    public function getStudyGroups(): array {
        return $this->studyGroups;
    }

    public function getKey(): Grade {
        return $this->grade;
    }

    public function addItem($item): void {
        $this->studyGroups[] = $item;
    }

    /**
     * @return StudyGroup[]
     */
    public function &getItems(): array {
        return $this->studyGroups;
    }
}