<?php

namespace App\Tools\TimetableGpuExport;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class Configuration {
    #[Assert\NotNull]
    public ?DateTime $start;

    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'start')]
    public ?DateTime $end;

    /** @var Week[] */
    #[Assert\Valid]
    public array $weeks = [ ];
}