<?php

namespace App\Form\Model;

use App\Entity\DateLesson;
use App\Entity\StudentAbsenceType;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateStudentBulkAbsence {
    public ?DateLesson $from = null;

    public ?DateLesson $until = null;

    public ?StudentAbsenceType $type = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $phone = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $message = null;
}