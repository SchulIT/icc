<?php

namespace App\Response\Book;

use App\Response\UuidTrait;
use JMS\Serializer\Annotation as Serializer;

class Subject {
    use UuidTrait;

    #[Serializer\SerializedName('name')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $name;

    private readonly string $abbreviation;

    public function __construct(string $uuid, string $name, string $abbreviation) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->abbreviation = $abbreviation;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getAbbreviation(): string {
        return $this->abbreviation;
    }
}