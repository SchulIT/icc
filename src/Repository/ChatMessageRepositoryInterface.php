<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessage;

interface ChatMessageRepositoryInterface {

    /**
     * @param Chat $chat
     * @param int $numberOfMessages Number of messages to return
     * @param ChatMessage|null $lastMessage The last message that was shown to the user (or null if none was shown)
     * @return ChatMessage[]
     */
    public function findByChatAndRange(Chat $chat, int $numberOfMessages, ChatMessage|null $lastMessage = null): array;

    public function persist(ChatMessage $message): void;

    public function remove(ChatMessage $message): void;
}