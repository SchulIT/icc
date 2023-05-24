<?php

namespace App\Notification;

interface NotificationHandlerInterface {
    public function canHandle(Notification $notification): bool;

    public function handle(Notification $notification): void;

    public function getName(): string;
}