<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Event\ExamImportEvent;
use App\Notification\ImportNotification;
use App\Notification\NotificationService;
use App\Repository\UserRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExamImportEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly TranslatorInterface     $translator, private readonly ExamSettings $examSettings,
                                private readonly UserRepositoryInterface $userRepository, private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly NotificationService     $notificationService) { }

    public function onExamsImported(ExamImportEvent $event) {
        if($this->examSettings->isNotificationsEnabled() !== true) {
            return;
        }

        $recipients = $this->userRepository->findAllByNotifyExams();

        foreach($recipients as $recipient) {
            $notification = new ImportNotification(
                $recipient,
                $this->translator->trans('exam.title', [], 'email'),
                $this->translator->trans('exam.content', [], 'email'),
                $this->urlGenerator->generate('exams', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('exam.link', [], 'email'),
                $this->examSettings->getNotificationSender(),
                $this->examSettings->getNotificationReplyToAddress()
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            ExamImportEvent::class => 'onExamsImported'
        ];
    }
}