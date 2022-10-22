<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={
 *      @ORM\UniqueConstraint(fields={"section", "grade", "teacher"})
 * })
 * @Auditable()
 */
class GradeTeacher {

    use IdTrait;
    use SectionAwareTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher", inversedBy="grades")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher|null
     */
    #[Assert\NotNull]
    private ?Teacher $teacher = null;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="teachers")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Grade|null
     */
    #[Assert\NotNull]
    private ?Grade $grade = null;

    /**
     * @ORM\Column(type="grade_teacher_type")
     * @var GradeTeacherType
     */
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