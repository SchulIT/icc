<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserNotificationSetting;
use App\Notification\NotificationDeliveryTarget;

class UserNotificationSettingRepository extends AbstractRepository implements UserNotificationSettingRepositoryInterface {

    public function findByUserAndTypeAndTarget(User $user, string $type, NotificationDeliveryTarget $target): ?UserNotificationSetting {
        return $this->em->getRepository(UserNotificationSetting::class)->findOneBy([
            'user' => $user,
            'type' => $type,
            'target' => $target
        ]);
    }

    public function persist(UserNotificationSetting $setting): void {
        $this->em->persist($setting);
        $this->em->flush();
    }
}