<?php

namespace App\View\Filter;

use App\Entity\Subject;

class SubjectFilterView implements FilterViewInterface {

    /** @var Subject|null */
    private $currectSubject;

    /** @var Subject[] */
    private $subjects;

    public function __construct(array $subjects, ?Subject $currectSubject = null) {
        $this->subjects = $subjects;
        $this->currectSubject = $currectSubject;
    }

    /**
     * @return Subject[]|array
     */
    public function getSubjects() {
        return $this->subjects;
    }

    /**
     * @return Subject|null
     */
    public function getCurrentSubject(): ?Subject {
        return $this->currectSubject;
    }

    public function isEnabled(): bool {
        return count($this->subjects) > 0;
    }
}