<?php

namespace App\Notification\EventSubscriber;

use App\Entity\BookComment;
use App\Entity\Teacher;
use App\Event\BookCommentCreatedEvent;
use App\Event\BookCommentUpdatedEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\Repository\UserRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookCommentEventSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly TranslatorInterface $translator,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly NotificationService $notificationService,
                                private readonly SectionResolverInterface $sectionResolver,
                                private readonly UserRepositoryInterface $userRepository) { }

    public function onBookCommentCreatedOrUpdate(BookCommentCreatedEvent|BookCommentUpdatedEvent $event): void {
        $teachers = $this->resolveTeachers($event->getComment());
        $users = $this->userRepository->findAllTeachers($teachers);

        $subjectKey = $event instanceof BookCommentCreatedEvent ? 'book_comment.create.title' : 'book_comment.update.title';
        $contentKey = $event instanceof BookCommentCreatedEvent ? 'book_comment.create.content' : 'book_comment.update.content';

        foreach($users as $recipient) {
            $notification = new Notification(
                $recipient,
                $this->translator->trans($subjectKey, [], 'email'),
                $this->translator->trans($contentKey, [], 'email'),
                $this->urlGenerator->generate('show_book_comment', [ 'uuid' => $event->getComment()->getUuid() ]),
                $this->translator->trans('book_comment.link', [], 'email'),
                true
            );

            $this->notificationService->notify($notification);
        }
    }

    private function resolveTeachers(BookComment $comment): array {
        $teachers = [ ];

        $section = $this->sectionResolver->getSectionForDate($comment->getDate());

        if($section === null) {
            return $teachers;
        }

        foreach($comment->getStudents() as $student) {
            $grade = $student->getGrade($section);

            if($grade === null) {
                continue;
            }

            foreach($grade->getTeachers() as $gradeTeacher) {
                if($gradeTeacher->getTeacher()->getId() !== $comment->getTeacher()->getId()) {
                    $teachers[] = $gradeTeacher->getTeacher();
                }
            }
        }

        // Create unique list of teachers
        return array_values(
            ArrayUtils::createArrayWithKeys(
                $teachers,
                fn(Teacher $teacher) => $teacher->getId()
            )
        );
    }

    public static function getSubscribedEvents(): array {
        return [
            BookCommentCreatedEvent::class => 'onBookCommentCreatedOrUpdate',
            BookCommentUpdatedEvent::class => 'onBookCommentCreatedOrUpdate'
        ];
    }
}