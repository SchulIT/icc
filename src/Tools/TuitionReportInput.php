<?php

namespace App\Tools;

use App\Entity\Section;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionReportInput {
    public array $types = [ ];

    #[Assert\NotNull]
    public ?Section $section = null;
}