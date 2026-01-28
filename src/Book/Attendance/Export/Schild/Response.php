<?php

namespace App\Book\Attendance\Export\Schild;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;

class Response {
    #[Assert\NotBlank]
    #[Serializer\SerializedName('firstname')]
    #[Serializer\Type('string')]
    public string $firstname;

    #[Assert\NotBlank]
    #[Serializer\SerializedName('lastname')]
    #[Serializer\Type('string')]
    public string $lastname;

    #[Serializer\SerializedName('birthday')]
    #[Serializer\Type("DateTime<'d\.m\.Y'>")]
    public DateTime $birthday;

    #[GreaterThan(0)]
    #[Serializer\SerializedName('section')]
    #[Serializer\Type('integer')]
    public int $section;

    #[Assert\GreaterThan(0)]
    #[Serializer\SerializedName('year')]
    #[Serializer\Type('integer')]
    public int $year;

    #[Serializer\SerializedName('absent')]
    public int $absentLessons = 0;

    #[Serializer\SerializedName('not_excused')]
    public int $notExcusedAbsentLessons = 0;
}