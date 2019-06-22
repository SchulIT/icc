<?php

namespace App\View\Filter;

use App\Entity\StudyGroup;
use App\Grouping\StudyGroupGradeGroup;

class StudyGroupFilterView {

    /** @var StudyGroupGradeGroup[] */
    private $studyGroupGroups;

    /** @var StudyGroup|null */
    private $currentStudyGroup;

    /**
     * StudyGroupFilterView constructor.
     * @param StudyGroupGradeGroup[] $studyGroupGroups
     * @param StudyGroup|null $studyGroup
     */
    public function __construct(array $studyGroupGroups, ?StudyGroup $studyGroup = null) {
        $this->studyGroupGroups = $studyGroupGroups;
        $this->currentStudyGroup = $studyGroup;
    }
    /**
     * @return StudyGroupGradeGroup[]
     */
    public function getStudyGroupGroups(): array {
        return $this->studyGroupGroups;
    }

    /**
     * @return StudyGroup|null
     */
    public function getCurrentStudyGroup(): ?StudyGroup {
        return $this->currentStudyGroup;
    }
}