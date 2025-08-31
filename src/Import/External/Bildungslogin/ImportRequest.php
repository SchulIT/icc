<?php

namespace App\Import\External\Bildungslogin;

use App\Entity\LearningManagementSystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ImportRequest {
    #[Assert\NotNull]
    public LearningManagementSystem|null $lms = null;

    #[Assert\NotNull]
    public ?File $summaryCsv = null;

    #[Assert\NotNull]
    public ?File $passwordsCsv = null;
}