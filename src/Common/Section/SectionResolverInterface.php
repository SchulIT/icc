<?php

namespace App\Common\Section;

use App\Common\Entity\Section;
use DateTime;

interface SectionResolverInterface {
    public function getSectionForDate(DateTime $dateTime): ?Section;

    public function getCurrentSection(): ?Section;
}