<?php

namespace App\Section;

use App\Entity\Section;
use DateTime;

interface SectionResolverInterface {
    public function getSectionForDate(DateTime $dateTime): ?Section;

    public function getCurrentSection(): ?Section;
}