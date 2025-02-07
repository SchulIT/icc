<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserNotificationSetting;
use App\Notification\NotificationDeliveryTarget;

interface UserNotificationSettingRepositoryInterface {
    public function findByUserAndTypeAndTarget(User $user, string $type, NotificationDeliveryTarget $target): ?UserNotificationSetting;

    public function persist(UserNotificationSetting $setting): void;
}