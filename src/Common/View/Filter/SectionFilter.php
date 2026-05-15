<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Section;
use App\Common\Repository\SectionRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Common\Settings\GeneralSettings;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\SectionFilterView;

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