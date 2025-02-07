<?php

namespace App\Notification\Delivery;

use App\Entity\UserNotificationSetting;

class AlwaysStrategy implements DeliveryStrategy {
    public function deliver(?UserNotificationSetting $setting): bool {
        return true;
    }

    public function getStrategyType(): DeliverStrategyType {
        return DeliverStrategyType::Always;
    }
}