<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessageAttachment;

class ChatMessageAttachmentRepository extends AbstractRepository implements ChatMessageAttachmentRepositoryInterface {

    public function findByChat(Chat $chat): array {
        return $this->em->createQueryBuilder()
            ->select(['a'])
            ->from(ChatMessageAttachment::class, 'a')
            ->leftJoin('a.message', 'm')
            ->where('m.chat = :chat')
            ->setParameter('chat', $chat)
            ->getQuery()
            ->getResult();
    }

    public function remove(ChatMessageAttachment $attachment): void {
        $this->em->remove($attachment);
        $this->em->flush();
    }
}