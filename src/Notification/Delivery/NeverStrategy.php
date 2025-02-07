<?php

namespace App\Notification\Delivery;

use App\Entity\UserNotificationSetting;

class NeverStrategy implements DeliveryStrategy {

    public function deliver(?UserNotificationSetting $setting): bool {
        return false;
    }

    public function getStrategyType(): DeliverStrategyType {
        return DeliverStrategyType::Never;
    }
}