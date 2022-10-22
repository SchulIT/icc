<?php

namespace App\View\Filter;

use App\Entity\Subject;

class SubjectsFilterView implements FilterViewInterface {

    /**
     * @param Subject[] $subjects
     * @param Subject[] $currentSubjects
     */
    public function __construct(private array $subjects, private array $currentSubjects)
    {
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