<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use App\Event\SubstitutionImportEvent;
use App\Notification\ImportNotification;
use App\Notification\NotificationService;
use App\Repository\UserRepositoryInterface;
use App\Settings\SubstitutionSettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class SubstitutionImportEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private TranslatorInterface  $translator, private SubstitutionSettings $substitutionSettings,
                                private UserRepositoryInterface $userRepository, private UrlGeneratorInterface $urlGenerator,
                                private NotificationService $notificationService) { }

    public function onSubstitutionsImported(SubstitutionImportEvent $event): void {
        if($this->substitutionSettings->isNotificationsEnabled() !== true) {
            return;
        }

        $recipients = $this->userRepository->findAllByNotifySubstitutions();

        foreach($recipients as $recipient) {
            $notification = new ImportNotification(
                self::getKey(),
                $recipient,
                $this->translator->trans('substitution.title', [], 'email'),
                $this->translator->trans('substitution.content', [], 'email'),
                $this->urlGenerator->generate('substitutions', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('substitution.link', [], 'email'),
                $this->substitutionSettings->getNotificationSender(),
                $this->substitutionSettings->getNotificationReplyToAddress()
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            SubstitutionImportEvent::class => 'onSubstitutionsImported'
        ];
    }


    public static function getSupportedRecipientUserTypes(): array {
        return UserType::cases();
    }

    public static function getKey(): string {
        return 'substitution_import';
    }

    public static function getLabelKey(): string {
        return 'notifications.substitution_import.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.substitution_import.help';
    }
}