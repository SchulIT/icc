<?php

namespace App\Tools\WestermannZvs;

use App\Entity\LearningManagementSystem;
use Symfony\Component\Validator\Constraints as Assert;

class CheckRequest {
    #[Assert\NotBlank]
    #[Assert\Json]
    public string|null $json = null;

    #[Assert\NotNull]
    public LearningManagementSystem|null $lms = null;
}