<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Event\SubstitutionImportEvent;
use App\Notification\ImportNotification;
use App\Notification\NotificationService;
use App\Repository\UserRepositoryInterface;
use App\Settings\SubstitutionSettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubstitutionImportEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly TranslatorInterface $translator, private readonly SubstitutionSettings $substitutionSettings,
                                private readonly UserRepositoryInterface $userRepository, private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly NotificationService $notificationService) { }

    public function onSubstitutionsImported(SubstitutionImportEvent $event): void {
        if($this->substitutionSettings->isNotificationsEnabled() !== true) {
            return;
        }

        $recipients = $this->userRepository->findAllByNotifySubstitutions();

        foreach($recipients as $recipient) {
            $notification = new ImportNotification(
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
}