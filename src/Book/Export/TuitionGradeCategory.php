<?php

namespace App\Book\Export;

class TuitionGradeCategory {

    private string $uuid;

    private string $displayName;

    /**
     * @return string
     */
    public function getUuid(): string {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return TuitionGradeCategory
     */
    public function setUuid(string $uuid): TuitionGradeCategory {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return TuitionGradeCategory
     */
    public function setDisplayName(string $displayName): TuitionGradeCategory {
        $this->displayName = $displayName;
        return $this;
    }
}