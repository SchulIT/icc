<?php

namespace App\View\Filter;

use App\Entity\StudyGroup;
use App\Grouping\StudyGroupGradeGroup;

class StudyGroupFilterView implements FilterViewInterface {

    /**
     * StudyGroupFilterView constructor.
     * @param StudyGroupGradeGroup[] $studyGroupGroups
     */
    public function __construct(private array $studyGroupGroups, private ?StudyGroup $currentStudyGroup = null)
    {
    }
    /**
     * @return StudyGroupGradeGroup[]
     */
    public function getStudyGroupGroups(): array {
        return $this->studyGroupGroups;
    }

    public function getCurrentStudyGroup(): ?StudyGroup {
        return $this->currentStudyGroup;
    }

    public function isEnabled(): bool {
        return count($this->studyGroupGroups) > 0;
    }
}