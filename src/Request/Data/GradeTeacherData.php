<?php

namespace App\Request\Data;

use App\Entity\GradeTeacherType;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeTeacherData {

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $grade = null;

    /**
     * Kürzel der Lehrkraft
     */
    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $teacher = null;

    /**
     * @see GradeTeacherType
     */
    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getGradeTeacherTypes')]
    #[Serializer\Type('string')]
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
        return array_map(fn(GradeTeacherType $type) => $type->value, GradeTeacherType::cases());
    }
}