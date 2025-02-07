<?php

namespace App\Notification\Delivery;

use App\Entity\UserNotificationSetting;

class OutOutStrategy implements DeliveryStrategy {

    public function deliver(?UserNotificationSetting $setting): bool {
        if($setting === null) {
            return true;
        }

        return $setting->isEnabled();
    }

    public function getStrategyType(): DeliverStrategyType {
        return DeliverStrategyType::OptOut;
    }
}