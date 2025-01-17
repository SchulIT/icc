<?php

namespace App\Tools\SubstitutionEvaluation;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class ReportInput {
    #[Assert\NotNull]
    public ?DateTime $start = null;

    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'start')]
    public ?DateTime $end = null;

    /**
     * @var string[]
     */
    #[Assert\Count(min: 1)]
    public array $substitutionTypes = [ ];
}