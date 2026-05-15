<?php

namespace App\Message\Event;

use App\Message\Entity\Message;
use Symfony\Contracts\EventDispatcher\Event;

class MessageUpdatedEvent extends Event {
    public function __construct(private Message $message)
    {
    }

    public function getMessage(): Message {
        return $this->message;
    }
}