<?php

namespace App\Grouping;

use App\Entity\SickNote;
use App\Entity\Tuition;

class SickNoteTuitionGroup implements GroupInterface, SortableGroupInterface {

    /** @var Tuition */
    private $tuition;

    /** @var SickNote[] */
    private $sickNotes = [ ];

    public function __construct(Tuition $tuition) {
        $this->tuition = $tuition;
    }

    /**
     * @return Tuition
     */
    public function getTuition(): Tuition {
        return $this->tuition;
    }

    /**
     * @return SickNote[]
     */
    public function getSickNotes(): array {
        return $this->sickNotes;
    }

    public function getKey() {
        return $this->tuition;
    }

    public function addItem($item) {
        $this->sickNotes[] = $item;
    }

    public function &getItems(): array {
        return $this->sickNotes;
    }
}