<?php

namespace App\Notification\WebPush;

use App\Entity\UserWebPushSubscription;

interface PushNotificationStrategyInterface {
    /**
     * @return UserWebPushSubscription[]
     */
    public function getSubscriptions($objective): array;

    /**
     * @param $objective
     * @return string
     */
    public function getTitle($objective): string;

    /**
     * @param $objective
     * @return string
     */
    public function getBody($objective): string;
}