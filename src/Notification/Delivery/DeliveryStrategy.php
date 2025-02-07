<?php

namespace App\Notification\Delivery;

use App\Entity\UserNotificationSetting;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notifications.delivery_strategy')]
interface DeliveryStrategy {

    public function deliver(UserNotificationSetting|null $setting): bool;

    public function getStrategyType(): DeliverStrategyType;
}