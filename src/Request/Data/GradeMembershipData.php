<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeMembershipData {

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $student = null;

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $grade = null;

    public function getStudent(): string {
        return $this->student;
    }

    public function setStudent(string $student): GradeMembershipData {
        $this->student = $student;
        return $this;
    }

    public function getGrade(): string {
        return $this->grade;
    }

    public function setGrade(string $grade): GradeMembershipData {
        $this->grade = $grade;
        return $this;
    }
}