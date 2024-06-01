<?php

namespace App\Book\Grade\Export\ZP10;

use App\Entity\Section;
use App\Entity\TuitionGradeCategory;
use Symfony\Component\Validator\Constraints as Assert;

class Configuration {

    #[Assert\NotNull]
    public TuitionGradeCategory|null $abschlussNote;

    #[Assert\NotNull]
    public TuitionGradeCategory|null $vornote;

    #[Assert\NotNull]
    public TuitionGradeCategory|null $schriftlich;

    #[Assert\NotNull]
    public TuitionGradeCategory|null $muendlich;

    #[Assert\NotNull]
    public Section|null $section;
}