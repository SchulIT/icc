<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $id = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $type = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    #[Assert\Count(min: 1)]
    private array $grades = [ ];

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $students = [ ];

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): StudyGroupData {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): StudyGroupData {
        $this->name = $name;
        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): StudyGroupData {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getGrades(): array {
        return $this->grades;
    }

    /**
     * @param string[] $grades
     */
    public function setGrades(array $grades): StudyGroupData {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param string[] $students
     */
    public function setStudents(array $students): StudyGroupData {
        $this->students = $students;
        return $this;
    }
}