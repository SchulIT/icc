<?php

namespace App\Notification\Repository;

use App\Common\Entity\User;
use App\Framework\Repository\AbstractRepository;
use App\Notification\Entity\UserNotificationSetting;
use App\Notification\NotificationDeliveryTarget;
use App\Notification\Repository\UserNotificationSettingRepositoryInterface;

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