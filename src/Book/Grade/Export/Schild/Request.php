<?php

namespace App\Book\Grade\Export\Schild;

use App\Request\JsonParam;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;

#[JsonParam]
class Request {

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

    #[Assert\GreaterThan(0)]
    #[Serializer\SerializedName('year')]
    #[Serializer\Type('integer')]
    public int $year;

    #[GreaterThan(0)]
    #[Serializer\SerializedName('section')]
    #[Serializer\Type('integer')]
    public int $section;

    #[Assert\NotBlank]
    #[Serializer\SerializedName('grade')]
    #[Serializer\Type('string')]
    public string $grade;
}