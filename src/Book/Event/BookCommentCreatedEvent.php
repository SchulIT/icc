<?php

namespace App\Book\Event;

use App\Book\Entity\BookComment;
use Symfony\Contracts\EventDispatcher\Event;

class BookCommentCreatedEvent extends Event {
    public function __construct(private readonly BookComment $comment) {
    }

    public function getComment(): BookComment {
        return $this->comment;
    }
}