<?php

namespace App\View\Filter;

use App\Entity\Section;

class SectionFilterView {

    /** @var Section[] */
    private $sections;

    /** @var Section|null */
    private $currentSection;

    public function __construct(array $sections, ?Section $currentSection) {
        $this->sections = $sections;
        $this->currentSection = $currentSection;
    }

    /**
     * @return Section[]
     */
    public function getSections(): array {
        return $this->sections;
    }

    /**
     * @return Section|null
     */
    public function getCurrentSection(): ?Section {
        return $this->currentSection;
    }
}