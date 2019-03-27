<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $type;

    /**
     * @Serializer\Type("array<string>")
     * @Assert\Count(min="1")
     * @var string|null
     */
    private $grades;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $students;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return StudyGroupData
     */
    public function setId(?string $id): StudyGroupData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return StudyGroupData
     */
    public function setName(?string $name): StudyGroupData {
        $this->name = $name;
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
     * @return StudyGroupData
     */
    public function setType(?string $type): StudyGroupData {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGrades(): ?string {
        return $this->grades;
    }

    /**
     * @param string|null $grades
     * @return StudyGroupData
     */
    public function setGrades(?string $grades): StudyGroupData {
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
     * @return StudyGroupData
     */
    public function setStudents(array $students): StudyGroupData {
        $this->students = $students;
        return $this;
    }
}