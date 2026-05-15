<?php

namespace App\Chat\Repository;

use App\Chat\Entity\Chat;
use App\Chat\Entity\ChatMessageAttachment;

interface ChatMessageAttachmentRepositoryInterface {

    /**
     * @param Chat $chat
     * @return ChatMessageAttachment[]
     */
    public function findByChat(Chat $chat): array;

    public function countByChat(Chat $chat): int;

    public function remove(ChatMessageAttachment $attachment): void;
}