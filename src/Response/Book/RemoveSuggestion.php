<?php

namespace App\Response\Book;

use JMS\Serializer\Annotation as Serializer;

class RemoveSuggestion {
    #[Serializer\Type(Student::class)]
    #[Serializer\SerializedName('student')]
    #[Serializer\ReadOnlyProperty]
    private readonly Student $student;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('reason')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $reason;

    #[Serializer\Type('array<int>')]
    #[Serializer\SerializedName('lessons')]
    #[Serializer\ReadOnlyProperty]
    private readonly array $lessons;

    public function __construct(Student $student, string $reason, array $lessons) {
        $this->student = $student;
        $this->reason = $reason;
        $this->lessons = $lessons;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @return string
     */
    public function getReason(): string {
        return $this->reason;
    }

    /**
     * @return int[]
     */
    public function getLessons(): array {
        return $this->lessons;
    }
}