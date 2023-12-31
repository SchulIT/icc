<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class AttendanceFlag {
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('uuid')]
    private string $uuid;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('description')]
    private string $description;

    public function getUuid(): string {
        return $this->uuid;
    }

    public function setUuid(string $uuid): AttendanceFlag {
        $this->uuid = $uuid;
        return $this;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): AttendanceFlag {
        $this->description = $description;
        return $this;
    }
}