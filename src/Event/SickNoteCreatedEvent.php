<?php

namespace App\Event;

use App\Entity\SickNote;
use Symfony\Contracts\EventDispatcher\Event;

class SickNoteCreatedEvent extends Event {

    private $sickNote;

    public function __construct(SickNote $sickNote) {
        $this->sickNote = $sickNote;
    }

    /**
     * @return SickNote
     */
    public function getSickNote(): SickNote {
        return $this->sickNote;
    }
}