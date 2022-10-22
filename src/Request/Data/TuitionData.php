<?php

namespace App\Request\Data;

use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionData {

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
    #[Assert\NotBlank(allowNull: true)]
    private ?string $displayName = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $subject = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private ?array $teachers = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $studyGroup = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): TuitionData {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): TuitionData {
        $this->name = $name;
        return $this;
    }

    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): TuitionData {
        $this->displayName = $displayName;
        return $this;
    }

    public function getSubject(): ?string {
        return $this->subject;
    }

    public function setSubject(?string $subject): TuitionData {
        $this->subject = $subject;
        return $this;
    }


    /**
     * @return string[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    /**
     * @param string[] $teachers
     */
    public function setTeachers(array $teachers): TuitionData {
        $this->teachers = $teachers;
        return $this;
    }

    public function getStudyGroup(): ?string {
        return $this->studyGroup;
    }

    public function setStudyGroup(?string $studyGroup): TuitionData {
        $this->studyGroup = $studyGroup;
        return $this;
    }
}