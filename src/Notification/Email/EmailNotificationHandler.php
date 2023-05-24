<?php

namespace App\Notification\Email;

use App\Notification\Notification;
use App\Notification\NotificationHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailNotificationHandler implements NotificationHandlerInterface {

    /**
     * @param EmailStrategyInterface[] $strategies
     */
    public function __construct(private readonly iterable $strategies, private readonly string $appName, private readonly string $sender,
                                private readonly MailerInterface  $mailer, private readonly Environment $twig, private readonly ?LoggerInterface $logger) {

    }

    public function canHandle(Notification $notification): bool {
        return true;
    }

    public function handle(Notification $notification): void {
        if($notification->getRecipient()->isEmailNotificationsEnabled() === false && $notification->isDeliveryEnforced() === false) {
            $this->logger->debug(sprintf('Empfänger %s hat E-Mail Benachrichtigungen deaktiviert, überspringe.', $notification->getRecipient()->getUsername()));
            return;
        }

        if(empty($notification->getRecipient()->getEmail())) {
            $this->logger->debug(sprintf('Empfänger %s hat keine E-Mail-Adresse hinterlegt, überspringe.', $notification->getRecipient()->getUsername()));
            return;
        }

        foreach($this->strategies as $strategy) {
            if($strategy->supports($notification) !== true) {
                $this->logger->debug(sprintf('Strategie %s unterstützt Benachrichtigungen vom Typ %s nicht', get_class($strategy), get_class($notification)));
                continue;
            }

            $replyTo = $strategy->getReplyTo($notification);

            $context = [
                'notification' => $notification,
                'sender' => $strategy->getSender($notification),
                'replyTo' => $replyTo
            ];

            $content = $this->twig->render(
                $strategy->getTemplate(),
                $context
            );

            $mail = (new Email())
                ->subject($notification->getSubject())
                ->from(new Address($this->sender, $this->appName))
                ->sender(new Address($this->sender, $this->appName))
                ->text($content)
                ->to($notification->getRecipient()->getEmail());

            if($strategy->getHtmlTemplate() !== null) {
                $html = $this->twig->render(
                    $strategy->getHtmlTemplate(),
                    $context
                );

                $mail->html($html);
            }

            if (!empty($replyTo)) {
                $mail->replyTo(new Address($replyTo, $strategy->getSender($notification)));
            }

            $this->mailer->send($mail);
        }
    }

    public function getName(): string {
        return 'email';
    }
}