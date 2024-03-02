<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\User;
use Doctrine\DBAL\Exception as DbalException;
use PHPUnit\Exception;
use function Doctrine\ORM\QueryBuilder;

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

    public function markAllChatMessagesSeen(Chat $chat, User $user): void {
        $qbOwnMessages = $this->em->createQueryBuilder()
            ->select('mInner.id')
            ->from(ChatMessage::class, 'mInner')
            ->where('mInner.chat = :chat')
            ->andWhere('mInner.createdBy = :user');

        $qbSeenMessages = $this->em->createQueryBuilder()
            ->select('mInner2.id')
            ->from(ChatMessage::class, 'mInner2')
            ->leftJoin('mInner2.seenBy', 'sbInner2')
            ->where('mInner2.chat = :chat')
            ->andWhere('sbInner2.id = :user');


        $qb = $this->em->createQueryBuilder();
        $qb->select('DISTINCT m.id')
            ->from(ChatMessage::class, 'm')
            ->where(
                $qb->expr()->notIn('m.id', $qbOwnMessages->getDQL())
            )
            ->andWhere(
                $qb->expr()->notIn('m.id', $qbSeenMessages->getDQL())
            )
            ->setParameter('user', $user->getId())
            ->setParameter('chat', $chat->getId());

        $messages = array_column($qb->getQuery()->getScalarResult(), 'id');

        foreach($messages as $messageId) {
            try {
                $query = $this->em->getConnection()->prepare('INSERT INTO chat_message_seen_by (chat_message_id, user_id) VALUES (:message, :user)');
                $query->bindValue('message', $messageId);
                $query->bindValue('user', $user->getId());

                $query->executeStatement();
            } catch (DbalException $e) {

            }
        }
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