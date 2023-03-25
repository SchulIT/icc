<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['tuition', 'category', 'student'])]
#[Auditable]
class TuitionGrade {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Tuition::class)]
    #[ORM\JoinColumn]
    #[Assert\NotNull]
    private ?Tuition $tuition = null;

    #[ORM\ManyToOne(targetEntity: TuitionGradeCategory::class)]
    #[ORM\JoinColumn]
    #[Assert\NotNull]
    private ?TuitionGradeCategory $category = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn]
    #[Assert\NotNull]
    private ?Student $student = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $encryptedGrade = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Tuition|null
     */
    public function getTuition(): ?Tuition {
        return $this->tuition;
    }

    /**
     * @param Tuition|null $tuition
     * @return TuitionGrade
     */
    public function setTuition(?Tuition $tuition): TuitionGrade {
        $this->tuition = $tuition;
        return $this;
    }

    /**
     * @return TuitionGradeCategory|null
     */
    public function getCategory(): ?TuitionGradeCategory {
        return $this->category;
    }

    /**
     * @param TuitionGradeCategory|null $category
     * @return TuitionGrade
     */
    public function setCategory(?TuitionGradeCategory $category): TuitionGrade {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return TuitionGrade
     */
    public function setStudent(?Student $student): TuitionGrade {
        $this->student = $student;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEncryptedGrade(): ?string {
        return $this->encryptedGrade;
    }

    /**
     * @param string|null $encryptedGrade
     * @return TuitionGrade
     */
    public function setEncryptedGrade(?string $encryptedGrade): TuitionGrade {
        $this->encryptedGrade = $encryptedGrade;
        return $this;
    }
}