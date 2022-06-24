<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Repository\SectionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\GeneralSettings;
use App\Utils\ArrayUtils;

class SectionFilter {
    private SectionRepositoryInterface $sectionRepository;
    private SectionResolverInterface $sectionResolver;

    public function __construct(SectionRepositoryInterface $sectionRepository, SectionResolverInterface $sectionResolver) {
        $this->sectionRepository = $sectionRepository;
        $this->sectionResolver = $sectionResolver;
    }

    public function handle(?string $sectionUuid): SectionFilterView {
        $sections = ArrayUtils::createArrayWithKeys(
            $this->sectionRepository->findAll(),
            function(Section $section) {
                return (string)$section->getUuid();
            });

        $section = $sectionUuid !== null ?
            $sections[$sectionUuid] ?? null : null;

        if($section === null) {
            $section = $this->sectionResolver->getCurrentSection();
        }

        return new SectionFilterView($sections, $section);
    }
}