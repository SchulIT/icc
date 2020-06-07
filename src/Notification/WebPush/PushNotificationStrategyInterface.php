<?php

namespace App\Notification\WebPush;

use App\Entity\UserWebPushSubscription;

interface PushNotificationStrategyInterface {

    /**
     * Returns whether this strategy supports the given objective. This controls
     * whether or not this strategy is executed.
     *
     * @param $objective
     * @return bool
     */
    public function supports($objective): bool;

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