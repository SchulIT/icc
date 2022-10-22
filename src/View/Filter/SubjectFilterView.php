<?php

namespace App\View\Filter;

use App\Entity\Subject;

class SubjectFilterView implements FilterViewInterface {

    /**
     * @param Subject[] $subjects
     */
    public function __construct(private array $subjects, private ?Subject $currectSubject = null)
    {
    }

    /**
     * @return Subject[]|array
     */
    public function getSubjects() {
        return $this->subjects;
    }

    public function getCurrentSubject(): ?Subject {
        return $this->currectSubject;
    }

    public function isEnabled(): bool {
        return count($this->subjects) > 0;
    }
}