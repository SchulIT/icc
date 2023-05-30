<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

trait UuidTrait {

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('uuid')]
    private ?string $uuid;

    /**
     * @return string|null
     */
    public function getUuid(): ?string {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     * @return self
     */
    public function setUuid(?string $uuid): self {
        $this->uuid = $uuid;
        return $this;
    }

}