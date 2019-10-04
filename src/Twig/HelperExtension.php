<?php

namespace App\Twig;

use App\Entity\Message;
use App\Message\DismissedMessagesHelper;
use App\Message\MessageConfirmationHelper;
use App\Utils\ColorUtils;
use App\Utils\RefererHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HelperExtension extends AbstractExtension {

    private $confirmationHelper;
    private $dismissedHelper;
    private $redirectHelper;
    private $colorUtils;

    public function __construct(MessageConfirmationHelper $confirmationHelper, DismissedMessagesHelper $dismissedHelper,
                                RefererHelper $redirectHelper, ColorUtils $colorUtils) {
        $this->confirmationHelper = $confirmationHelper;
        $this->dismissedHelper = $dismissedHelper;
        $this->redirectHelper = $redirectHelper;
        $this->colorUtils = $colorUtils;
    }

    public function getFunctions() {
        return [
            new TwigFunction('is_confirmed', [ $this, 'isConfirmed' ]),
            new TwigFunction('is_dismissed', [ $this, 'isDismissed' ]),
            new TwigFunction('referer_path', [ $this, 'refererPath' ]),
            new TwigFunction('foreground', [ $this, 'foregroundColor' ])
        ];
    }

    public function isConfirmed(Message $message) {
        return $this->confirmationHelper->isMessageConfirmed($message);
    }

    public function isDismissed(Message $message) {
        return $this->dismissedHelper->isMessageDismissed($message);
    }

    public function refererPath(array $mapping, string $route, array $parameters = [ ]): string {
        return $this->redirectHelper->getRefererPathFromQuery($mapping, $route, $parameters);
    }

    public function foregroundColor(string $color): string {
        return $this->colorUtils->getForeground($color);
    }
}