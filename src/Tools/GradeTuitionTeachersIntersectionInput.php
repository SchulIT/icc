<?php

namespace App\Tools;

use App\Entity\Section;
use Symfony\Component\Validator\Constraints as Assert;

class GradeTuitionTeachersIntersectionInput {

    #[Assert\Count(min: 1)]
    public array $leftGrades = [ ];

    #[Assert\Count(min: 1)]
    public array $rightGrades = [ ];

    #[Assert\Count(min: 1)]
    public array $subjects = [ ];

    #[Assert\NotNull]
    public ?Section $section = null;

}