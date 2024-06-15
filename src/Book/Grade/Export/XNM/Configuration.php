<?php

namespace App\Book\Grade\Export\XNM;

use App\Entity\Section;
use App\Entity\Tuition;
use App\Entity\TuitionGradeCategory;
use Symfony\Component\Validator\Constraints as Assert;

class Configuration {

    #[Assert\NotNull]
    public TuitionGradeCategory|null $notenKategorie;

    #[Assert\NotNull]
    public Section|null $section;

    /** @var Tuition[] */
    #[Assert\Count(min: 1)]
    public array $tuitions = [ ];
}