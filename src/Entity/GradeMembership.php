<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={
 *      @ORM\UniqueConstraint(fields={"section", "grade", "student"})
 * })
 * @Auditable()
 */
class GradeMembership {

    use IdTrait;
    use SectionAwareTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="gradeMemberships")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?Student $student = null;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="memberships")
     */
    #[Assert\NotNull]
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