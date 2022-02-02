<?php

namespace App\Grouping;

use App\Entity\SickNote;

class SickNoteGenericGroup implements GroupInterface, SortableGroupInterface {

    /** @var SickNote[] */
    private $sickNotes = [ ];

    public function __construct() {    }

    /**
     * @return SickNote[]
     */
    public function getSickNotes(): array {
        return $this->sickNotes;
    }

    public function getKey() {
        return null;
    }

    public function addItem($item) {
        $this->sickNotes[] = $item;
    }

    public function &getItems(): array {
        return $this->sickNotes;
    }
}