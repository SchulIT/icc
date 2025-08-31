<?php

namespace App\Import\External\WestermannZsv;

use App\Entity\LearningManagementSystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ImportRequest {
    #[Assert\NotNull]
    public ?LearningManagementSystem $lms = null;

    #[Assert\NotNull]
    public ?File $csv = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 1)]
    public string $delimiter = ';';
}