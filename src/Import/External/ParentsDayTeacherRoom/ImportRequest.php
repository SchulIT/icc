<?php

namespace App\Import\External\ParentsDayTeacherRoom;

use App\Entity\ParentsDay;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ImportRequest {
    #[Assert\NotNull]
    public ?ParentsDay $parentsDay = null;

    #[Assert\NotNull]
    public ?File $csv = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 1)]
    public string $delimiter = ';';

    #[Assert\NotBlank]
    public string $teacherHeader = 'Lehrer';

    #[Assert\NotBlank]
    public string $roomHeader = 'Raum';
}