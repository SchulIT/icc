<?php

namespace App\Event;

use App\Entity\BookComment;
use Symfony\Contracts\EventDispatcher\Event;

class BookCommentCreatedEvent extends Event {
    public function __construct(private readonly BookComment $comment) {
    }

    public function getComment(): BookComment {
        return $this->comment;
    }
}