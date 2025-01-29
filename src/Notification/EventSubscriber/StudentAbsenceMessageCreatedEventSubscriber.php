<?php

namespace App\Notification\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use App\Event\StudentAbsenceMessageCreatedEvent;
use App\Notification\NotificationService;
use App\Notification\StudentAbsenceNotification;
use App\StudentAbsence\InvolvedUsersResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class StudentAbsenceMessageCreatedEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private InvolvedUsersResolver $involvedUsersResolver, private NotificationService $notificationService,
                                private UrlGeneratorInterface $urlGenerator, private TranslatorInterface $translator) {

    }

    public function onMessageCreated(StudentAbsenceMessageCreatedEvent $event): void {
        $recipients = [ ];
        foreach($this->involvedUsersResolver->resolveUsers($event->getAbsence()) as $user) {
            if($event->getMessage()->getCreatedBy()->getId() !== $user->getId()) {
                $recipients[] = $user;
            }
        }

        $emails = array_map(fn(User $user) => $user->getEmail(), $recipients);
        if(!empty($event->getAbsence()->getEmail()) && !in_array($event->getAbsence()->getEmail(), $emails) && $event->getAbsence()->getCreatedBy()->getId() !== $event->getMessage()->getCreatedBy()->getId()) {
            $recipients[] = (new User())
                ->setEmail($event->getAbsence()->getEmail())
                ->setUsername($event->getAbsence()->getEmail());
        }

        foreach($recipients as $recipient) {
            $notification = new StudentAbsenceNotification(
                self::getKey(),
                $recipient,
                $this->translator->trans('student_absence.message.title', [], 'email'),
                $this->translator->trans('student_absence.message.content', [], 'email'),
                $this->urlGenerator->generate('show_student_absence', [ 'uuid' => $event->getAbsence()->getUuid()->toString()], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('student_absence.link', [], 'email'),
                $event->getAbsence()
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            StudentAbsenceMessageCreatedEvent::class => 'onMessageCreated'
        ];
    }

    public static function getSupportedRecipientUserTypes(): array {
        return [
            UserType::Teacher,
            UserType::Parent,
            UserType::Student
        ];
    }

    public static function getKey(): string {
        return 'student_absence_message_created';
    }

    public static function getLabelKey(): string {
        return 'notifications.student_absence_message_created.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.student_absence_message_created.help';
    }
}