<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessage;

class ChatMessageRepository extends AbstractRepository  implements ChatMessageRepositoryInterface {

    public function findByChatAndRange(Chat $chat, int $numberOfMessages, ?ChatMessage $lastMessage = null): array {
        $qb = $this->em->createQueryBuilder()
            ->select(['m', 'a', 'u'])
            ->from(ChatMessage::class, 'm')
            ->leftJoin('m.attachments', 'a')
            ->leftJoin('m.createdBy', 'u')
            ->where('m.chat = :chat');

        if($lastMessage !== null) {
            $qb->andWhere('m.id < :lastId')
                ->setParameter('lastId', $lastMessage->getId());
        }

        $qb->setMaxResults($numberOfMessages);

        return $qb->getQuery()->getResult();
    }

    public function persist(ChatMessage $message): void {
        $this->em->persist($message);
        $this->em->flush();
    }

    public function remove(ChatMessage $message): void {
        $this->em->remove($message);
        $this->em->flush();
    }
}