<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use App\Event\ExamImportEvent;
use App\Notification\ImportNotification;
use App\Notification\NotificationService;
use App\Repository\UserRepositoryInterface;
use App\Settings\ExamSettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class ExamImportEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private TranslatorInterface     $translator, private ExamSettings $examSettings,
                                private UserRepositoryInterface $userRepository, private UrlGeneratorInterface $urlGenerator,
                                private NotificationService     $notificationService) { }

    public function onExamsImported(ExamImportEvent $event): void {
        if($this->examSettings->isNotificationsEnabled() !== true) {
            return;
        }

        $recipients = $this->userRepository->findAllByNotifyExams();

        foreach($recipients as $recipient) {
            $notification = new ImportNotification(
                self::getKey(),
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

    public static function getSupportedRecipientUserTypes(): array {
        return UserType::cases();
    }

    public static function getKey(): string {
        return 'exam_import';
    }

    public static function getLabelKey(): string {
        return 'notifications.exam_import.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.exam_import.help';
    }
}