<?php

namespace App\View\Filter;

use App\Entity\Subject;

class SubjectsFilterView implements FilterViewInterface {

    /** @var Subject[] */
    private $subjects;

    /** @var Subject[] */
    private $currentSubjects;

    /**
     * @param Subject[] $subjects
     * @param Subject[] $currentSubjects
     */
    public function __construct(array $subjects, array $currentSubjects) {
        $this->subjects = $subjects;
        $this->currentSubjects = $currentSubjects;
    }

    /**
     * @return Subject[]
     */
    public function getSubjects(): array {
        return $this->subjects;
    }

    /**
     * @return Subject[]
     */
    public function getCurrentSubjects(): array {
        return $this->currentSubjects;
    }

    /*public function isSelected(Subject $subject): bool {
        foreach($this->currentSubjects as $s) {
            if($s->getId() === $subject->getId()) {
                return true;
            }
        }

        return false;
    }*/

    public function isEnabled(): bool {
        return count($this->subjects) > 0;
    }
}