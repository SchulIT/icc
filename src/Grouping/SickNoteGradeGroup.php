<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\SickNote;

class SickNoteGradeGroup implements GroupInterface, SortableGroupInterface {

    /** @var Grade */
    private $grade;

    /** @var SickNote[] */
    private $sickNotes = [ ];

    public function __construct(Grade $grade) {
        $this->grade = $grade;
    }

    public function getGrade(): Grade {
        return $this->grade;
    }

    /**
     * @return SickNote[]
     */
    public function getSickNotes(): array {
        return $this->sickNotes;
    }

    public function getKey() {
        return $this->grade;
    }

    public function addItem($item) {
        $this->sickNotes[] = $item;
    }

    public function &getItems(): array {
        return $this->sickNotes;
    }
}