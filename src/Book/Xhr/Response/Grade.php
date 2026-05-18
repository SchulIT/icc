<?php

namespace App\Book\Xhr\Response;

use App\Book\Xhr\Response\UuidTrait;
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