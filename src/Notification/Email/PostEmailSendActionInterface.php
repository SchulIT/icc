<?php

namespace App\Notification\Email;

interface PostEmailSendActionInterface {
    public function onNotificationSent($objective): void;
}