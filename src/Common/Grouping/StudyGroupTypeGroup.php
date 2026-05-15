<?php

namespace App\Common\Grouping;

use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupType;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<StudyGroupType, StudyGroup>
 */
class StudyGroupTypeGroup implements GroupInterface, SortableGroupInterface {

    /** @var StudyGroup[] */
    private $studyGroups;

    public function __construct(private readonly StudyGroupType $type)
    {
    }

    public function getKey(): StudyGroupType {
        return $this->type;
    }

    public function addItem($item): void {
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