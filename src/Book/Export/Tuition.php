<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Tuition {

    use UuidTrait;

    /**
     * The ID which is specified as ID when importing students.
     */
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('id')]
    private ?string $id = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('name')]
    private ?string $name = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('subject')]
    private ?string $subject = null;

    /**
     * @var Teacher[]
     */
    #[Serializer\Type('array<App\Book\Export\Teacher>')]
    #[Serializer\SerializedName('teachers')]
    private array $teachers = [ ];

    public function getId(): string {
        return $this->id;
    }

    public function setId(string $id): Tuition {
        $this->id = $id;
        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): Tuition {
        $this->subject = $subject;
        return $this;
    }

    public function addTeacher(Teacher $teacher): void {
        $this->teachers[] = $teacher;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }
}