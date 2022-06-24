<?php

namespace App\Notification\Email;

use App\Settings\NotificationSettings;
use App\Utils\EnumArrayUtils;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailNotificationService {

    private array $blacklistDomains = [ 'example.com' ];

    private bool $isEnabled;
    private string $appName;
    private string $sender;
    private Swift_Mailer $mailer;
    private Environment $twig;
    private NotificationSettings $settings;
    private ?LoggerInterface $logger;

    public function __construct(bool $isEnabled, string $appName, string $sender, Swift_Mailer $mailer, Environment $twig, NotificationSettings $notificationSettings, LoggerInterface $logger = null) {
        $this->isEnabled = $isEnabled;
        $this->appName = $appName;
        $this->sender = $sender;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->settings = $notificationSettings;
        $this->logger = $logger;
    }

    public function sendNotification($objective, EmailStrategyInterface $strategy) {
        if($this->isEnabled === false) {
            $this->logger->info('E-Mail notifications are disabled. Skip sending them.');
            return;
        }

        if($strategy->isEnabled() === false) {
            $this->logger->info(sprintf('E-Mail notifications for strategy %s are disabled. Skip sending them.', get_class($strategy)));
            return;
        }

        foreach($strategy->getRecipients($objective) as $recipient) {
            if(EnumArrayUtils::inArray($recipient->getUserType(), $this->settings->getEmailEnabledUserTypes()) !== true) {
                continue;
            }

            if(empty($recipient->getEmail())) {
                continue;
            }

            if($this->isBlacklistedEmailDomain($recipient->getEmail())) {
                continue;
            }

            $content = $this->twig->render($strategy->getTemplate(), [
                'objective' => $objective,
                'sender' => $strategy->getSender($objective)
            ]);

            $message = (new Swift_Message)
                ->setSubject($strategy->getSubject($objective))
                ->setFrom([$this->sender], $this->appName)
                ->setSender($this->sender, $this->appName)
                ->setBody($content, 'text/html')
                ->setTo([ $recipient->getEmail() ]);

            $replyTo = $strategy->getReplyTo($objective);

            if (!empty($replyTo)) {
                $message->setReplyTo($replyTo, $strategy->getSender($objective));
            }

            $this->mailer->send($message);
        }

        if($strategy instanceof PostEmailSendActionInterface) {
            $strategy->onNotificationSent($objective);
        }
    }

    private function isBlacklistedEmailDomain(string $email): bool {
        foreach($this->blacklistDomains as $blacklistDomain) {
            $suffix = sprintf('@%s', $blacklistDomain);
            if(substr($email, -strlen($suffix)) === $suffix) {
                return true;
            }
        }

        return false;
    }
}