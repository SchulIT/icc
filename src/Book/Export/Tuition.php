<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Tuition {

    /**
     * The ID which is specified as ID when importing students.
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("id")
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     * @var string
     */
    private $name;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("subject")
     * @var string
     */
    private $subject;

    /**
     * @Serializer\Type("array<App\Book\Export\Teacher>")
     * @Serializer\SerializedName("teachers")
     * @var Teacher[]
     */
    private $teachers = [ ];

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Tuition
     */
    public function setId(string $id): Tuition {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Tuition
     */
    public function setName(string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Tuition
     */
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