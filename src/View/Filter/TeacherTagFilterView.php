<?php

namespace App\View\Filter;

use App\Entity\TeacherTag;

class TeacherTagFilterView implements FilterViewInterface {

    /** @var TeacherTag[] */
    private $tags;

    /** @var TeacherTag|null */
    private $currentTag;

    public function __construct(array $tags, ?TeacherTag $currentTag) {
        $this->tags = $tags;
        $this->currentTag = $currentTag;
    }

    /**
     * @return TeacherTag[]
     */
    public function getTags(): array {
        return $this->tags;
    }

    /**
     * @return TeacherTag|null
     */
    public function getCurrentTag(): ?TeacherTag {
        return $this->currentTag;
    }

    public function isEnabled(): bool {
        return count($this->tags) > 0;
    }
}