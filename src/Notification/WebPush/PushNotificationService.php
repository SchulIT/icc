<?php

namespace App\Notification\WebPush;

use App\Entity\User;
use App\Entity\UserWebPushSubscription;
use App\Settings\NotificationSettings;
use App\Utils\EnumArrayUtils;
use BenTools\WebPushBundle\Model\Message\PushNotification;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerRegistry;
use BenTools\WebPushBundle\Sender\PushMessageSender;
use Exception;
use Psr\Log\LoggerInterface;

class PushNotificationService {

    private $subscriptionManager;
    private $sender;
    private $settings;
    private $logger;

    public function __construct(UserSubscriptionManagerRegistry $subscriptionManager, PushMessageSender $sender, NotificationSettings $settings, LoggerInterface $logger) {
        $this->subscriptionManager = $subscriptionManager;
        $this->sender = $sender;
        $this->settings = $settings;
        $this->logger = $logger;
    }

    public function sendNotifications($objective, PushNotificationStrategyInterface $strategy) {
        $subscriptions = $strategy->getSubscriptions($objective);
        $subscriptions = array_filter($subscriptions, function(UserWebPushSubscription $subscription) {
            /** @var User $user */
            $user = $subscription->getUser();

            return EnumArrayUtils::inArray($user->getUserType(), $this->settings->getPushEnabledUserTypes());
        });

        $notification = new PushNotification($strategy->getTitle($objective), [
            PushNotification::BODY => $strategy->getBody($objective)
        ]);

        $this->sender->setMaxPaddingLength(2000);

        try {
            $responses = $this->sender->push($notification->createMessage(), $subscriptions);

            foreach ($responses as $response) {
                if ($response->isExpired()) {
                    $this->subscriptionManager->delete($response->getSubscription());
                }
            }

            if ($strategy instanceof PostPushSendActionInterface) {
                $strategy->onNotificationSent($objective);
            }
        } catch (Exception $e) {
            $this->logger
                ->error('Failed to send web push notifications.', [
                    'exception' => $e
                ]);
        }
    }
}