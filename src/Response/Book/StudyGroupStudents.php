<?php

namespace App\Response\Book;

use JMS\Serializer\Annotation as Serializer;

class StudyGroupStudents {

    #[Serializer\SerializedName('study_group')]
    #[Serializer\Type(StudyGroup::class)]
    #[Serializer\ReadOnlyProperty]
    private readonly StudyGroup $studyGroup;

    /**
     * @var Student[]
     */
    #[Serializer\SerializedName('students')]
    #[Serializer\Type('array<' .  Student::class .'>')]
    #[Serializer\ReadOnlyProperty]
    private readonly array $students;

    public function __construct(StudyGroup $studyGroup, array $students) {
        $this->studyGroup = $studyGroup;
        $this->students = $students;
    }

    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }
}