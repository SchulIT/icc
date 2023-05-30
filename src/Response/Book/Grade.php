<?php

namespace App\Response\Book;

use App\Response\UuidTrait;
use JMS\Serializer\Annotation as Serializer;

class Grade {
    use UuidTrait;

    #[Serializer\SerializedName('name')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $name;

    public function __construct(string $uuid, string $name) {
        $this->uuid = $uuid;
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }
}