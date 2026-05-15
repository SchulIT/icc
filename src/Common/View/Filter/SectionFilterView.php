<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;

class SectionFilterView {

    /**
     * @param Section[] $sections
     */
    public function __construct(private array $sections, private ?Section $currentSection)
    {
    }

    /**
     * @return Section[]
     */
    public function getSections(): array {
        return $this->sections;
    }

    public function getCurrentSection(): ?Section {
        return $this->currentSection;
    }
}