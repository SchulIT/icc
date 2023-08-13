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

    public function __construct(Student $student, string $reason) {
        $this->student = $student;
        $this->reason = $reason;
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
}