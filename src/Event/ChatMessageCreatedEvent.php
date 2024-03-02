<?php

namespace App\Event;

use App\Entity\ChatMessage;
use Symfony\Contracts\EventDispatcher\Event;

class ChatMessageCreatedEvent extends Event {
    public function __construct(private readonly ChatMessage $message) {

    }

    /**
     * @return ChatMessage
     */
    public function getMessage(): ChatMessage {
        return $this->message;
    }
}