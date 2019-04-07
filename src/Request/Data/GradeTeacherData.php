<?php

namespace App\Request\Data;

use App\Entity\GradeTeacherType;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class GradeTeacherData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $grade;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $teacher;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getGradeTeacherTypes")
     * @see GradeTeacherType
     * @var string
     */
    private $type;

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @param string|null $grade
     * @return GradeTeacherData
     */
    public function setGrade(?string $grade): GradeTeacherData {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTeacher(): ?string {
        return $this->teacher;
    }

    /**
     * @param string|null $teacher
     * @return GradeTeacherData
     */
    public function setTeacher(?string $teacher): GradeTeacherData {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return GradeTeacherData
     */
    public function setType(?string $type): GradeTeacherData {
        $this->type = $type;
        return $this;
    }

    public static function getGradeTeacherTypes() {
        return array_values(GradeTeacherType::toArray());
    }
}