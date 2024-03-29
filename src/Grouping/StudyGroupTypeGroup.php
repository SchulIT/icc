<?php

namespace App\Grouping;

use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;

class StudyGroupTypeGroup implements GroupInterface, SortableGroupInterface {

    /** @var StudyGroup[] */
    private $studyGroups;

    public function __construct(private StudyGroupType $type)
    {
    }

    public function getKey() {
        return $this->type;
    }

    public function addItem($item) {
        $this->studyGroups[] = $item;
    }

    public function &getItems(): array {
        return $this->studyGroups;
    }

    public function getType(): StudyGroupType {
        return $this->type;
    }

    public function getStudyGroups(): array {
        return $this->studyGroups;
    }
}