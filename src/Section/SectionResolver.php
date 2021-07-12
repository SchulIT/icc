<?php

namespace App\Section;

use App\Entity\Section;
use App\Repository\SectionRepositoryInterface;
use App\Settings\GeneralSettings;
use DateTime;

class SectionResolver implements SectionResolverInterface {
    private $settings;
    private $sectionRepository;

    public function __construct(GeneralSettings $settings, SectionRepositoryInterface $sectionRepository) {
        $this->settings = $settings;
        $this->sectionRepository = $sectionRepository;
    }

    public function getSectionForDate(DateTime $dateTime): ?Section {
        return $this->sectionRepository->findOneByDate($dateTime);
    }

    public function getCurrentSection(): ?Section {
        $id = $this->settings->getCurrentSectionId();

        if($id === null) {
            return null;
        }

        return $this->sectionRepository->findOneById($id);
    }
}