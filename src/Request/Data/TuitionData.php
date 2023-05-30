<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TuitionData {

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $id = null;

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $name = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Serializer\Type('string')]
    private ?string $displayName = null;

    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    private ?string $subject = null;

    /**
     * @var string[]
     */
    #[Serializer\Type('array<string>')]
    private ?array $teachers = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Serializer\Type('string')]
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