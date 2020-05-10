<?php

namespace App\Notification\Email;

use App\Entity\UserType;
use App\Utils\EnumArrayUtils;
use Psr\Log\LoggerInterface;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailNotificationService {
    private $isEnabled;
    private $appName;
    private $sender;
    private $mailer;
    private $twig;
    private $urlGenerator;
    private $logger;

    public function __construct(bool $isEnabled, $appName, string $sender, \Swift_Mailer $mailer, Environment $twig, UrlGeneratorInterface $urlGenerator, LoggerInterface $logger = null) {
        $this->isEnabled = $isEnabled;
        $this->appName = $appName;
        $this->sender = $sender;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }

    public function sendNotification($objective, EmailStrategyInterface $strategy) {
        if($this->isEnabled === false) {
            $this->logger->info('E-Mail notifications are disabled. Skip sending them.');
            return;
        }

        if($strategy->isEnabled() === false) {
            $this->logger->info(sprintf('E-Mail notifications for strategy %s are disabled. Skipt sending them.', get_class($strategy)));
            return;
        }

        foreach($strategy->getRecipients($objective) as $recipient) {
            if(EnumArrayUtils::inArray($recipient->getUserType(), $this->getAllowedUserTypesForNotifications()) !== true) {
                continue;
            }

            if(empty($recipient->getEmail())) {
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

    /**
     * @return UserType[]
     */
    public function getAllowedUserTypesForNotifications(): array {
        return [ UserType::Teacher() ];
    }
}