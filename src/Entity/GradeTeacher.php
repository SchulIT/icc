<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['section', 'grade', 'teacher'])]
class GradeTeacher {

    use IdTrait;
    use SectionAwareTrait;

    /**
     * @var Teacher|null
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'grades')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Teacher $teacher = null;

    /**
     * @var Grade|null
     */
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Grade::class, inversedBy: 'teachers')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Grade $grade = null;

    /**
     * @var GradeTeacherType
     */
    #[ORM\Column(type: 'grade_teacher_type')]
    private GradeTeacherType $type;

    public function __construct() {
        $this->type = GradeTeacherType::Primary();
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): GradeTeacher {
        $this->teacher = $teacher;
        return $this;
    }

    public function getGrade(): ?Grade {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): GradeTeacher {
        $this->grade = $grade;
        return $this;
    }

    public function getType(): GradeTeacherType {
        return $this->type;
    }

    public function setType(GradeTeacherType $type): GradeTeacher {
        $this->type = $type;
        return $this;
    }
}