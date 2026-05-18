<?php

namespace App\Book\Xhr\Response;

use JMS\Serializer\Annotation as Serializer;

trait UuidTrait {

    #[Serializer\SerializedName('uuid')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $uuid;

    public function getUuid(): string {
        return $this->uuid;
    }
}