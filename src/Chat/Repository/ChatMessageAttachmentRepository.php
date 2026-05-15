<?php

namespace App\Chat\Repository;

use App\Chat\Entity\Chat;
use App\Chat\Entity\ChatMessageAttachment;
use App\Framework\Repository\AbstractRepository;
use App\Chat\Repository\ChatMessageAttachmentRepositoryInterface;

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

    public function countByChat(Chat $chat): int {
        return $this->em->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(ChatMessageAttachment::class, 'a')
            ->leftJoin('a.message', 'm')
            ->where('m.chat = :chat')
            ->setParameter('chat', $chat)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function remove(ChatMessageAttachment $attachment): void {
        $this->em->remove($attachment);
        $this->em->flush();
    }
}