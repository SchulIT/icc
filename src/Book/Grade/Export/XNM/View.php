<?php

namespace App\Book\Grade\Export\XNM;

use App\Entity\Section;

class View {
    /**
     * @param Row[] $rows
     */
    public function __construct(private readonly array $rows, private readonly Section $section) {

    }

    public function getSection(): Section {
        return $this->section;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array {
        return $this->rows;
    }
}