<?php

namespace App\Book\Excuse;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
readonly class AssociateExcuseNoteMessage {
    public function __construct(
        public int $excuseNoteId
    ) {

    }
}