<?php

namespace App\Notification\Email;

use App\Notification\Notification;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class DefaultEmailStrategy implements EmailStrategyInterface {

    public function __construct(#[Autowire(env: 'MAILER_FROM')] private string $sender) { }

    public function supports(Notification $notification): bool {
        return get_class($notification) === Notification::class;
    }

    public function getReplyTo(Notification $notification): ?string {
        return null;
    }

    public function getSender(Notification $notification): string {
        return $this->sender;
    }

    public function getTemplate(): string {
        return 'email/default.txt.twig';
    }

    public function getHtmlTemplate(): ?string {
        return 'email/default.html.twig';
    }
}