<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Repository\SectionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\GeneralSettings;
use App\Utils\ArrayUtils;

class SectionFilter {
    public function __construct(private SectionRepositoryInterface $sectionRepository, private SectionResolverInterface $sectionResolver)
    {
    }

    public function handle(?string $sectionUuid): SectionFilterView {
        $sections = ArrayUtils::createArrayWithKeys(
            $this->sectionRepository->findAll(),
            fn(Section $section) => (string)$section->getUuid());

        $section = $sectionUuid !== null ?
            $sections[$sectionUuid] ?? null : null;

        if($section === null) {
            $section = $this->sectionResolver->getCurrentSection();
        }

        return new SectionFilterView($sections, $section);
    }
}