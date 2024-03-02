<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessageAttachment;

interface ChatMessageAttachmentRepositoryInterface {

    /**
     * @param Chat $chat
     * @return ChatMessageAttachment[]
     */
    public function findByChat(Chat $chat): array;

    public function remove(ChatMessageAttachment $attachment): void;
}