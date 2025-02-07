<?php

namespace App\Notification;

use App\Entity\User;
use App\Entity\UserNotificationSetting;
use App\Repository\UserNotificationSettingRepositoryInterface;

readonly class UserNotificationSettingSaver {

    public function __construct(private UserNotificationSettingRepositoryInterface $userNotificationSettingRepository) {

    }

    public function persist(User $user, string $type, NotificationDeliveryTarget $target, bool $isEnabled): void {
        $setting = $this->userNotificationSettingRepository->findByUserAndTypeAndTarget($user, $type, $target);

        if($setting === null) {
            $setting = (new UserNotificationSetting())
                ->setUser($user)
                ->setType($type)
                ->setTarget($target);
        }

        $setting->setIsEnabled($isEnabled);
        $this->userNotificationSettingRepository->persist($setting);
    }
}