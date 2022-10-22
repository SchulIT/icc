<?php

namespace App\Event;

use App\Entity\Message;
use Symfony\Contracts\EventDispatcher\Event;

class MessageCreatedEvent extends Event {
    public function __construct(private Message $message)
    {
    }

    public function getMessage(): Message {
        return $this->message;
    }
}