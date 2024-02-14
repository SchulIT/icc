<?php

namespace App\Notification\Pushover;

use App\Notification\Notification;
use App\Notification\NotificationHandlerInterface;
use App\Settings\NotificationSettings;
use App\Utils\ArrayUtils;
use Exception;
use Psr\Log\LoggerInterface;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification as PushoverNotification;
use Serhiy\Pushover\Api\Message\Priority;
use Serhiy\Pushover\Api\Message\Sound;
use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;

class PushoverNotificationHandler implements NotificationHandlerInterface {

    private ?Application $application = null;

    public function __construct(private readonly NotificationSettings $notificationSettings, private readonly LoggerInterface $logger) { }

    public function canHandle(Notification $notification): bool {
        return !empty($this->notificationSettings->getPushoverApiToken())
            && !empty($notification->getRecipient()->getPushoverToken())
            && ArrayUtils::inArray($notification->getRecipient()->getUserType(), $this->notificationSettings->getPushoverEnabledUserTypes()) !== false;
    }

    private function initialize(): void {
        if($this->application !== null) {
            return;
        }

        $this->application = new Application($this->notificationSettings->getPushoverApiToken());
    }

    public function handle(Notification $notification): void {
        $this->initialize();

        try {
            $recipient = new Recipient($notification->getRecipient()->getPushoverToken());
            $message = new Message($notification->getContent(), $notification->getSubject());
            if (!empty($notification->getLink()) && !empty($notification->getLinkText())) {
                $message->setUrl($notification->getLink());
                $message->setUrlTitle($notification->getLinkText());
            }
            $message->setPriority(new Priority(Priority::NORMAL));

            $pushoverNotification = new PushoverNotification($this->application, $recipient, $message);
            $pushoverNotification->setSound(new Sound(Sound::PUSHOVER));

            $response = $pushoverNotification->push();

            if (!$response->isSuccessful()) {
                $this->logger->alert('Pushover-Benachrichtigung wurde nicht erfolgreich abgeschickt.', [
                    'errors' => $response->getErrors(),
                    'recipient' => $notification->getRecipient()->getUserIdentifier()
                ]);
            }
        } catch (Exception $e) {
            $this->logger->alert('Pushover-Benachrichtigung wurde nicht erfolgreich abgeschickt.', [
                'errors' => $e->getMessage(),
                'recipient' => $notification->getRecipient()->getUserIdentifier()
            ]);
        }
    }

    public function getName(): string {
        return 'pushover';
    }
}