<?php

namespace App\Book\Grade\Export\Schild;

use JMS\Serializer\Annotation as Serializer;

class Tuition {
    #[Serializer\SerializedName('teacher')]
    public string $teacher;

    #[Serializer\SerializedName('subject')]
    public string $subject;

    #[Serializer\SerializedName('course')]
    public ?string $course;

    #[Serializer\SerializedName('grade')]
    public ?string $grade = null;

    #[Serializer\SerializedName('absent_lessons')]
    public int $absentLessons = 0;

    #[Serializer\SerializedName('non_excused_lessons')]
    public int $nonExcusedLessons = 0;
}