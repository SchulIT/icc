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
     * @Assert\NotNull()
     * @var Teacher|null
     */
    private ?Teacher $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="teachers")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Grade|null
     */
    private ?Grade $grade;

    /**
     * @ORM\Column(type="grade_teacher_type")
     * @var GradeTeacherType
     */
    private GradeTeacherType $type;

    public function __construct() {
        $this->type = GradeTeacherType::Primary();
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return GradeTeacher
     */
    public function setTeacher(?Teacher $teacher): GradeTeacher {
        $this->teacher = $teacher;
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
     * @return GradeTeacher
     */
    public function setGrade(?Grade $grade): GradeTeacher {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return GradeTeacherType
     */
    public function getType(): GradeTeacherType {
        return $this->type;
    }

    /**
     * @param GradeTeacherType $type
     * @return GradeTeacher
     */
    public function setType(GradeTeacherType $type): GradeTeacher {
        $this->type = $type;
        return $this;
    }
}