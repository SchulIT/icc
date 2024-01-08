<?php

namespace App\Entity;

use App\Validator\DateInActiveSection;
use App\Validator\DateInSection;
use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\NotNull;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['section', 'grade', 'student'])]
class GradeLimitedMembership {

    use IdTrait;
    use UuidTrait;
    use SectionAwareTrait;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'gradeLimitedMemberships')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Student $student = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Grade::class, inversedBy: 'limitedMemberships')]
    private ?Grade $grade = null;

    #[ORM\Column(type: 'date')]
    #[DateInSection(propertyPath: 'section')]
    #[Assert\NotNull]
    private ?DateTime $until;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): GradeLimitedMembership {
        $this->student = $student;
        return $this;
    }

    public function getGrade(): ?Grade {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): GradeLimitedMembership {
        $this->grade = $grade;
        return $this;
    }

    public function getUntil(): ?DateTime {
        return $this->until;
    }

    public function setUntil(?DateTime $until): GradeLimitedMembership {
        $this->until = $until;
        return $this;
    }

    public function __toString(): string {
        return sprintf('%s (%s) (%s)', $this->getStudent(), $this->getSection(), $this->getUntil()->format('Y-m-d'));
    }
}