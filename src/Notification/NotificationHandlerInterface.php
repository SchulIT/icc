<?php

namespace App\Notification;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notifications.handler')]
interface NotificationHandlerInterface {
    public function canHandle(Notification $notification): bool;

    public function handle(Notification $notification): void;

    public function getName(): string;
}