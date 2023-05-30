<?php

namespace App\Response\Book;

use App\Response\UuidTrait;
use JMS\Serializer\Annotation as Serializer;

class Teacher {

    use UuidTrait;

    #[Serializer\SerializedName('acronym')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $acronym;

    #[Serializer\SerializedName('title')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly ?string $title;

    #[Serializer\SerializedName('firstname')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $firstname;

    #[Serializer\SerializedName('lastname')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $lastname;

    public function __construct(string $uuid, string $acronym, string $firstname, string $lastname, ?string $title) {
        $this->uuid = $uuid;
        $this->acronym = $acronym;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->title = $title;
    }

    public function getAcronym(): string {
        return $this->acronym;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }
}