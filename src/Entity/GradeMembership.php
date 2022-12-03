<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['section', 'grade', 'student'])]
class GradeMembership {

    use IdTrait;
    use SectionAwareTrait;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'gradeMemberships')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Student $student = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Grade::class, inversedBy: 'memberships')]
    private ?Grade $grade = null;

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): GradeMembership {
        $this->student = $student;
        return $this;
    }

    public function getGrade(): ?Grade {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): GradeMembership {
        $this->grade = $grade;
        return $this;
    }
}