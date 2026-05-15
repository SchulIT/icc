<?php

declare(strict_types=1);

namespace App\Message\Twig;

use App\Message\DismissedMessagesHelper;
use App\Message\Entity\Message;
use App\Message\MessageConfirmationHelper;
use Twig\Attribute\AsTwigFunction;

readonly class MessageExtension {
    public function __construct(
        private MessageConfirmationHelper $confirmationHelper,
        private DismissedMessagesHelper $dismissedHelper,
    ) { }

    #[AsTwigFunction('is_confirmed')]
    public function isConfirmed(Message $message): bool {
        return $this->confirmationHelper->isMessageConfirmed($message);
    }

    #[AsTwigFunction('is_dismissed')]
    public function isDismissed(Message $message): bool {
        return $this->dismissedHelper->isMessageDismissed($message);
    }
}