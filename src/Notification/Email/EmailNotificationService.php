<?php

namespace App\Notification\Email;

use App\Settings\NotificationSettings;
use App\Utils\ArrayUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EmailNotificationService {

    private array $blacklistDomains = [ 'example.com' ];

    public function __construct(private bool $isEnabled, private string $appName, private string $sender, private MailerInterface $mailer, private Environment $twig, private NotificationSettings $settings, private ?LoggerInterface $logger = null)
    {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendNotification($objective, EmailStrategyInterface $strategy) {
        if($this->isEnabled === false) {
            $this->logger->info('E-Mail notifications are disabled. Skip sending them.');
            return;
        }

        if($strategy->isEnabled() === false) {
            $this->logger->info(sprintf('E-Mail notifications for strategy %s are disabled. Skip sending them.', $strategy::class));
            return;
        }

        foreach($strategy->getRecipients($objective) as $recipient) {
            if(ArrayUtils::inArray($recipient->getUserType(), $this->settings->getEmailEnabledUserTypes()) !== true) {
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

            $mail = (new Email())
                ->subject($strategy->getSubject($objective))
                ->from(new Address($this->sender, $this->appName))
                ->sender(new Address($this->sender, $this->appName))
                ->html($content)
                ->to($recipient->getEmail());

            $replyTo = $strategy->getReplyTo($objective);

            if (!empty($replyTo)) {
                $mail->replyTo(new Address($replyTo, $strategy->getSender($objective)));
            }

            $this->mailer->send($mail);
        }

        if($strategy instanceof PostEmailSendActionInterface) {
            $strategy->onNotificationSent($objective);
        }
    }

    private function isBlacklistedEmailDomain(string $email): bool {
        foreach($this->blacklistDomains as $blacklistDomain) {
            $suffix = sprintf('@%s', $blacklistDomain);
            if(str_ends_with($email, $suffix)) {
                return true;
            }
        }

        return false;
    }
}