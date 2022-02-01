<?php

namespace App\Grouping;

use App\Entity\SickNote;
use App\Entity\Student;

class SickNoteStudentGroup implements GroupInterface, SortableGroupInterface {

    /** @var Student */
    private $student;

    /** @var SickNote[] */
    private $sickNotes = [ ];

    public function __construct(Student $student) {
        $this->student = $student;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @return SickNote[]
     */
    public function getSickNotes(): array {
        return $this->sickNotes;
    }

    public function getKey() {
        return $this->getStudent();
    }

    public function addItem($item) {
        $this->sickNotes[] = $item;
    }

    public function &getItems(): array {
        return $this->sickNotes;
    }
}