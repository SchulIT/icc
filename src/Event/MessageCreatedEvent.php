<?php

namespace App\Event;

use App\Entity\Message;
use Symfony\Contracts\EventDispatcher\Event;

class MessageCreatedEvent extends Event {
    public function __construct(private readonly Message $message, private readonly bool $preventDatabaseActions)
    {
    }

    /**
     * @return bool Whether you are prohibited from performing any database actions on the entity (true) or not (false)
     */
    public function preventDatabaseActions(): bool {
        return $this->preventDatabaseActions;
    }

    public function getMessage(): Message {
        return $this->message;
    }
}