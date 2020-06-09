<?php

namespace App\Notification\WebPush;

use App\Entity\UserWebPushSubscription;
use App\Settings\NotificationSettings;
use App\Utils\EnumArrayUtils;
use BenTools\WebPushBundle\Model\Message\PushNotification;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerRegistry;
use BenTools\WebPushBundle\Sender\PushMessageSender;

class PushNotificationService {

    private $subscriptionManager;
    private $sender;
    private $settings;

    public function __construct(UserSubscriptionManagerRegistry $subscriptionManager, PushMessageSender $sender, NotificationSettings $settings) {
        $this->subscriptionManager = $subscriptionManager;
        $this->sender = $sender;
        $this->settings = $settings;
    }

    public function sendNotifications($objective, PushNotificationStrategyInterface $strategy) {
        $subscriptions = $strategy->getSubscriptions($objective);
        $subscriptions = array_filter($subscriptions, function(UserWebPushSubscription $subscription) {
            return EnumArrayUtils::inArray($subscription->getUser()->getUserType(), $this->settings->getPushEnabledUserTypes());
        });

        $notification = new PushNotification($strategy->getTitle($objective), [
            PushNotification::BODY => $strategy->getBody($objective)
        ]);

        $responses = $this->sender->push($notification->createMessage(), $subscriptions);

        foreach($responses as $response) {
            if($response->isExpired()) {
                $this->subscriptionManager->delete($response->getSubscription());
            }
        }

        if($strategy instanceof PostPushSendActionInterface) {
            $strategy->onNotificationSent($objective);
        }
    }
}