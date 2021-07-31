<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Repository\SectionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\GeneralSettings;
use App\Utils\ArrayUtils;

class SectionFilter {
    private $sectionRepository;
    private $sectionResolver;
    private $settings;

    public function __construct(SectionRepositoryInterface $sectionRepository, SectionResolverInterface $sectionResolver, GeneralSettings $settings) {
        $this->sectionRepository = $sectionRepository;
        $this->sectionResolver = $sectionResolver;
        $this->settings = $settings;
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