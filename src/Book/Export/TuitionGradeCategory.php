<?php

namespace App\Book\Export;

class TuitionGradeCategory {

    private string $uuid;

    private string $displayName;

    public function getUuid(): string {
        return $this->uuid;
    }

    public function setUuid(string $uuid): TuitionGradeCategory {
        $this->uuid = $uuid;
        return $this;
    }

    public function getDisplayName(): string {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): TuitionGradeCategory {
        $this->displayName = $displayName;
        return $this;
    }
}