<?php

namespace App\Notification\Delivery;

use App\Entity\UserNotificationSetting;

class OptInStrategy implements DeliveryStrategy {

    public function deliver(UserNotificationSetting|null $setting): bool {
        if($setting === null) {
            return false;
        }

        return $setting->isEnabled();
    }

    public function getStrategyType(): DeliverStrategyType {
        return DeliverStrategyType::OptIn;
    }
}