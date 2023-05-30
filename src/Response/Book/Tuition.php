<?php

namespace App\Response\Book;

use App\Response\UuidTrait;
use JMS\Serializer\Annotation as Serializer;

class Tuition {

    use UuidTrait;

    #[Serializer\SerializedName('name')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $name;

    #[Serializer\SerializedName('subject')]
    #[Serializer\Type(Subject::class)]
    #[Serializer\ReadOnlyProperty]
    private readonly Subject $subject;

    /**
     * @var Teacher[]
     */
    #[Serializer\SerializedName('teachers')]
    #[Serializer\Type('array<' . Teacher::class . '>')]
    #[Serializer\ReadOnlyProperty]
    private readonly array $teachers;

    #[Serializer\SerializedName('study_group')]
    #[Serializer\Type(StudyGroup::class)]
    #[Serializer\ReadOnlyProperty]
    private readonly StudyGroup $studyGroup;

    public function __construct(string $uuid, string $name, Subject $subject, StudyGroup $studyGroup, array $teachers) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->subject = $subject;
        $this->studyGroup = $studyGroup;
        $this->teachers = $teachers;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getSubject(): Subject {
        return $this->subject;
    }

    public function getTeachers(): array {
        return $this->teachers;
    }

    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }
}