<?php

namespace App\Twig;

use App\Entity\Message;
use App\Message\DismissedMessagesHelper;
use App\Message\MessageConfirmationHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HelperExtension extends AbstractExtension {

    private $confirmationHelper;
    private $dismissedHelper;

    public function __construct(MessageConfirmationHelper $confirmationHelper, DismissedMessagesHelper $dismissedHelper) {
        $this->confirmationHelper = $confirmationHelper;
        $this->dismissedHelper = $dismissedHelper;
    }

    public function getFunctions() {
        return [
            new TwigFunction('is_confirmed', [ $this, 'isConfirmed' ]),
            new TwigFunction('is_dismissed', [ $this, 'isDismissed' ])
        ];
    }

    public function isConfirmed(Message $message) {
        return $this->confirmationHelper->isMessageConfirmed($message);
    }

    public function isDismissed(Message $message) {
        return $this->dismissedHelper->isMessageDismissed($message);
    }
}