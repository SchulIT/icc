<?php

namespace App\Response\Book;

use App\Response\UuidTrait;
use JMS\Serializer\Annotation as Serializer;

class Student {

    use UuidTrait;

    #[Serializer\SerializedName('firstname')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $firstname;

    #[Serializer\SerializedName('lastname')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $lastname;

    #[Serializer\SerializedName('grade')]
    #[Serializer\Type(Grade::class)]
    #[Serializer\ReadOnlyProperty]
    private readonly ?Grade $grade;

    public function __construct(string $uuid, string $firstname, string $lastname, ?Grade $grade = null) {
        $this->uuid = $uuid;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->grade = $grade;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function getGrade(): ?Grade {
        return $this->grade;
    }
}