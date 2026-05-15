<?php

namespace App\Common\View\Filter;

use App\Common\Entity\TeacherTag;
use App\Framework\View\Filter\FilterViewInterface;

class TeacherTagFilterView implements FilterViewInterface {

    /**
     * @param TeacherTag[] $tags
     */
    public function __construct(private array $tags, private ?TeacherTag $currentTag)
    {
    }

    /**
     * @return TeacherTag[]
     */
    public function getTags(): array {
        return $this->tags;
    }

    public function getCurrentTag(): ?TeacherTag {
        return $this->currentTag;
    }

    public function isEnabled(): bool {
        return count($this->tags) > 0;
    }
}