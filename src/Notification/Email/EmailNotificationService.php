<?php

namespace App\Notification\Email;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EmailNotificationService {
    private $isEnabled;
    private $baseUrl;
    private $sender;
    private $mailer;
    private $twig;
    private $urlGenerator;
    private $logger;

    public function __construct(bool $isEnabled, string $baseUrl, string $sender, \Swift_Mailer $mailer, Environment $twig, UrlGeneratorInterface $urlGenerator, LoggerInterface $logger = null) {
        $this->isEnabled = $isEnabled;
        $this->baseUrl = $baseUrl;
        $this->sender = $sender;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }


}