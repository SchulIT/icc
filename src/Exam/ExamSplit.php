<?php

namespace App\Exam;

use App\Entity\Room;
use App\Entity\Student;
use Symfony\Component\Validator\Constraints as Assert;

class ExamSplit {

    #[Assert\NotNull]
    public ?Student $firstStudent = null;

    #[Assert\NotNull]
    public ?Student $lastStudent = null;

    #[Assert\NotNull]
    public ?Room $room = null;

    #[Assert\NotBlank]
    public ?string $description = null;
}