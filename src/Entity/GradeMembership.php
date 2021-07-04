<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
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
     * @Assert\NotNull()
     * @var Student|null
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="memberships")
     * @Assert\NotNull()
     * @var Grade|null
     */
    private $grade;

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return GradeMembership
     */
    public function setStudent(?Student $student): GradeMembership {
        $this->student = $student;
        return $this;
    }

    /**
     * @return Grade|null
     */
    public function getGrade(): ?Grade {
        return $this->grade;
    }

    /**
     * @param Grade|null $grade
     * @return GradeMembership
     */
    public function setGrade(?Grade $grade): GradeMembership {
        $this->grade = $grade;
        return $this;
    }
}