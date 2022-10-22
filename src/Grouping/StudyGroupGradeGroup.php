<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\StudyGroup;

class StudyGroupGradeGroup implements GroupInterface, SortableGroupInterface {

    /** @var StudyGroup[] */
    private array $studyGroups = [ ];

    public function __construct(private Grade $grade)
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

    public function getKey() {
        return $this->grade;
    }

    public function addItem($item) {
        $this->studyGroups[] = $item;
    }

    /**
     * @return StudyGroup[]
     */
    public function &getItems(): array {
        return $this->studyGroups;
    }
}