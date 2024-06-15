<?php

namespace App\Exam;

use Symfony\Component\Validator\Constraints as Assert;

class ExamSplitConfiguration {

    /**
     * @var ExamSplit[]
     */
    #[Assert\Count(min: 1)]
    public array $splits = [ ];
}