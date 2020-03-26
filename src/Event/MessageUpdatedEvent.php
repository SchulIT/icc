<?php

namespace App\Event;

use App\Entity\Message;
use Symfony\Contracts\EventDispatcher\Event;

class MessageUpdatedEvent extends Event {
    /** @var Message */
    private $message;

    public function __construct(Message $message) {
        $this->message = $message;
    }

    public function getMessage(): Message {
        return $this->message;
    }
}