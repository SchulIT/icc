<?php

namespace App\Doctrine;

use App\Converter\UserStringConverter;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\User;
use App\EventSubscriber\DoctrineEntityCollector;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsDoctrineListener(event: Events::preUpdate)]
class ChatMessageRecipientsSubscriber {

    public function __construct(private readonly TokenStorageInterface $tokenStorage,
                                private readonly TranslatorInterface $translator,
                                private readonly DoctrineEntityCollector $entityCollector,
                                private readonly UserStringConverter $userStringConverter) {

    }

    public function preUpdate(PreUpdateEventArgs $args): void {
        $currentUser = $this->tokenStorage->getToken()?->getUser();

        if(!$currentUser instanceof User) {
            return;
        }

        $uow = $args->getObjectManager()->getUnitOfWork();

        foreach($uow->getScheduledCollectionUpdates() as $collectionUpdate) {
            if(!$collectionUpdate->getOwner() instanceof Chat) {
                continue;
            }

            $chat = $collectionUpdate->getOwner();
            assert($chat instanceof Chat);

            $addedParticipants = [ ];
            $removedParticipants = [ ];

            foreach($collectionUpdate->getInsertDiff() as $user) {
                $addedParticipants[] = $user;
            }

            foreach($collectionUpdate->getDeleteDiff() as $user) {
                $removedParticipants[] = $user;
            }

            $content = '';

            if(count($addedParticipants) > 0) {
                $content = $this->translator->trans('chat.participants.success.added', [
                    '%users%' => implode(', ', array_map(fn(User $user) => $this->userStringConverter->convert($user, includeUsername: false), $addedParticipants)),
                ]);
            }

            if(count($removedParticipants) > 0) {
                $content .= "\n\n" . $this->translator->trans('chat.participants.success.remove', [
                    '%users%' => implode(', ', array_map(fn(User $user) => $this->userStringConverter->convert($user, includeUsername: false), $removedParticipants)),
                ]);
            }

            $chatMessage = (new ChatMessage())
                ->setChat($chat)
                ->setContent(trim($content))
                ->setCreatedBy($currentUser);

            $this->entityCollector->collectForPersist($chatMessage);
        }
    }
}