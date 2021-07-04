<?php

namespace App\View\Filter;

use App\Entity\Section;
use App\Repository\SectionRepositoryInterface;
use App\Settings\GeneralSettings;
use App\Utils\ArrayUtils;

class SectionFilter {
    private $sectionRepository;
    private $settings;

    public function __construct(SectionRepositoryInterface $sectionRepository, GeneralSettings $settings) {
        $this->sectionRepository = $sectionRepository;
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

        if($section === null && $this->settings->getCurrentSectionId() !== null) {
            $section = $this->sectionRepository->findOneById($this->settings->getCurrentSectionId());
        }

        return new SectionFilterView($sections, $section);
    }
}