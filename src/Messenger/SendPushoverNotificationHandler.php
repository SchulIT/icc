<?php

namespace App\Messenger;

use App\Repository\UserRepositoryInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification;
use Serhiy\Pushover\Api\Message\Priority;
use Serhiy\Pushover\Api\Message\Sound;
use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendPushoverNotificationHandler {

    private ?Application $application = null;

    public function __construct(private readonly ?string $pushoverToken, private readonly UserRepositoryInterface $userRepository, private readonly LoggerInterface $logger) {

    }

    private function initialize(): void {
        if($this->application !== null) {
            return;
        }

        $this->application = new Application($this->pushoverToken);
    }

    public function __invoke(SendPushoverNotificationMessage $message): void {
        try {
            $this->initialize();

            $user = $this->userRepository->findOneById($message->recipientId);

            if($user === null) {
                $this->logger->notice('Pushover-Benachrichtigung verworfen, da Benutzer nicht mehr existiert.', [
                    'recipient' => $message->recipientUserIdentifier
                ]);
                return;
            }

            if(empty($user->getPushoverToken())) {
                $this->logger->notice('Pushover-Benachrichtigung verworfen, da Benutzer kein Token hinterlegt hat.', [
                    'recipient' => $message->recipientUserIdentifier
                ]);
                return;
            }

            $recipient = new Recipient($user->getPushoverToken());
            $pushoverMessage = new Message($message->content, $message->subject);
            $pushoverMessage->setPriority(new Priority(Priority::NORMAL));

            if(!empty($message->link) && !empty($message->linkText)) {
                $pushoverMessage->setUrl($message->link);
                $pushoverMessage->setUrlTitle($message->linkText);
            }

            $notification = new Notification($this->application, $recipient, $pushoverMessage);
            $notification->setSound(new Sound(Sound::PUSHOVER));

            $response = $notification->push();

            if(!$response->isSuccessful()) {
                $this->logger->alert('Pushover-Benachrichtigung wurde nicht erfolgreich abgeschickt.', [
                    'errors' => $response->getErrors(),
                    'recipient' => $message->recipientUserIdentifier
                ]);
            }
        } catch (Exception $e) {
            $this->logger->alert('Pushover-Benachrichtigung wurde nicht erfolgreich abgeschickt.', [
                'errors' => $e->getMessage(),
                'recipient' => $message->recipientUserIdentifier
            ]);
        }
    }
}