<?php

namespace App\Message\Messenger;

use App\Message\Repository\MessageRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class RemoveMessageMessageHandler {

    public function __construct(
        private MessageRepositoryInterface $messageRepository
    ) {

    }

    public function __invoke(RemoveMessageMessage $message): string {
        $entity = $this->messageRepository->findOneById($message->messageId);

        if($entity === null) {
            return 'Message not found';
        }

        $this->messageRepository->remove($entity);
        return 'Message removed';
    }
}