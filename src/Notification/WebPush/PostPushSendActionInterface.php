<?php

namespace App\Notification\WebPush;

interface PostPushSendActionInterface {
    public function onNotificationSent($objective): void;
}