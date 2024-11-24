<?php

namespace App\Notification\EventSubscriber;

use App\Entity\StudyGroup;
use App\Event\SubstitutionMentionCreatedEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\Repository\UserRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudyGroupStrategy;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubstitutionMentionCreatedEventSubscriber implements EventSubscriber {

    public function __construct(private readonly UserRepositoryInterface $userRepository,
                                private readonly NotificationService $notificationService,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly TranslatorInterface $translator,
                                private readonly Sorter $sorter) {

    }

    public function onSubstitutionMentionCreated(SubstitutionMentionCreatedEvent $event): void {
        $substitution = $event->getSubstitution();
        $studyGroups = array_unique(array_merge(
            $event->getSubstitution()->getStudyGroups()->toArray(),
            $event->getSubstitution()->getReplacementStudyGroups()->toArray()
        ));

        $this->sorter->sort($studyGroups, StudyGroupStrategy::class);
        $studyGroupsAsString = implode(', ', array_map(fn(StudyGroup $studyGroup) => $studyGroup->getName(), $studyGroups));

        if(empty($studyGroupsAsString)) {
            $studyGroupsAsString = $this->translator->trans('substitution.mention.create.empty_studygroup', domain: 'email');
        }

        $subject = $substitution->getSubject() ?? $substitution->getReplacementSubject();

        if(empty($subject)) {
            $subject = $this->translator->trans('substitution.mention.create.empty_subject', domain: 'email');
        }

        foreach($this->userRepository->findAllTeachers([$event->getTeacher()]) as $recipient) {
            $notification = new Notification(
                $recipient,
                $this->translator->trans('substitution.mention.create.title', domain: 'email'),
                $this->translator->trans(
                    'substitution.mention.create.content',
                    parameters: [
                        '%date%' => $event->getSubstitution()->getDate()->format($this->translator->trans('date.format_short')),
                        '%lesson%' => $this->translator->trans('label.substitution_lessons', [
                            '%start%' => $substitution->getLessonStart(),
                            '%end%' => $substitution->getLessonEnd(),
                            '%count%' => $substitution->getLessonEnd() - $substitution->getLessonStart()
                        ]),
                        '%studygroup%' => $studyGroupsAsString,
                        '%subject%' => $subject,
                        '%acronym%' => $event->getTeacher()->getAcronym()
                    ],
                    domain: 'email'),
                $this->urlGenerator->generate('substitutions', ['date' => $event->getSubstitution()->getDate()->format('Y-m-d')], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->translator->trans('substitution.link', domain: 'email')
            );

            $this->notificationService->notify($notification);
        }
    }

    public function getSubscribedEvents(): array {
        return [
            SubstitutionMentionCreatedEvent::class => 'onSubstitutionMentionCreated'
        ];
    }
}