<?php

namespace App\Notification\Repository;

use App\Common\Entity\User;
use App\Notification\Entity\UserNotificationSetting;
use App\Notification\NotificationDeliveryTarget;

interface UserNotificationSettingRepositoryInterface {
    public function findByUserAndTypeAndTarget(User $user, string $type, NotificationDeliveryTarget $target): ?UserNotificationSetting;

    public function persist(UserNotificationSetting $setting): void;
}