<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class MessageList {

    /**
     * @Serializer\SerializedName("messages")
     * @Serializer\Type("array<App\Response\Api\V1\Message>")
     *
     * @var Message[]
     */
    private ?array $messages = null;

    /**
     * @return Message[]
     */
    public function getMessages(): array {
        return $this->messages;
    }

    /**
     * @param Message[] $messages
     */
    public function setMessages(array $messages): MessageList {
        $this->messages = $messages;
        return $this;
    }
}