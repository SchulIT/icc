<?php

namespace App\Request\Data;

use App\Entity\GradeTeacherType;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeTeacherData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $grade = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $teacher = null;

    /**
     * @Serializer\Type("string")
     * @see GradeTeacherType
     */
    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getGradeTeacherTypes')]
    private ?string $type = null;

    public function getGrade(): ?string {
        return $this->grade;
    }

    public function setGrade(?string $grade): GradeTeacherData {
        $this->grade = $grade;
        return $this;
    }

    public function getTeacher(): ?string {
        return $this->teacher;
    }

    public function setTeacher(?string $teacher): GradeTeacherData {
        $this->teacher = $teacher;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): GradeTeacherData {
        $this->type = $type;
        return $this;
    }

    public static function getGradeTeacherTypes() {
        return array_values(GradeTeacherType::toArray());
    }
}