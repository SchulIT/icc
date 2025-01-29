<?php

namespace App\Notification\EventSubscriber;

use App\Converter\StudentStringConverter;
use App\Entity\GradeTeacher;
use App\Entity\UserType;
use App\Event\StudentAbsentWithoutAnyNoteEvent;
use App\Notification\Notification;
use App\Notification\NotificationService;
use App\Repository\UserRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Settings\BookSettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class StudentAbsentWithoutAnyNoteGradeTeachersEventSubscriber implements EventSubscriberInterface, NotifierInterface {

    public function __construct(private NotificationService $notificationService, private UserRepositoryInterface $userRepository,
                                private TranslatorInterface $translator, private StudentStringConverter $studentStringConverter,
                                private BookSettings $bookSettings, private SectionResolverInterface $sectionResolver, private UrlGeneratorInterface $urlGenerator) {

    }

    public function onStudentAbsentWithoutAnyNote(StudentAbsentWithoutAnyNoteEvent $event): void {
        if($this->bookSettings->getNotifyGradeTeachersOnStudentAbsenceWithoutSuggestion() !== true) {
            return;
        }

        $student = $this->studentStringConverter->convert($event->getAttendance()->getStudent());
        $date = $event->getAttendance()->getDate()?->format($this->translator->trans('date.format'));
        $lessonNumber = $event->getAttendance()->getLesson();

        $grade = $event->getAttendance()->getStudent()->getGrade($this->sectionResolver->getCurrentSection());

        if($grade === null) {
            return;
        }

        $link = $this->urlGenerator->generate('book_student_attendance', [
            'student' => $event->getAttendance()->getStudent()->getUuid(),
            'grade' => $grade->getUuid()
        ]);
        $linkText = $this->translator->trans('attendance.absent_without_note.link', [], 'email');

        $gradeTeachers = $grade->getTeachers()->map(fn(GradeTeacher $teacher) => $teacher->getTeacher())->toArray();
        $recipients = $this->userRepository->findAllTeachers($gradeTeachers);

        foreach($recipients as $recipient) {
            $notification = new Notification(
                self::getKey(),
                $recipient,
                $this->translator->trans('attendance.absent_without_note.title', ['%student%' => $student], 'email'),
                $this->translator->trans('attendance.absent_without_note.content', ['%student%' => $student, '%date%' => $date, '%lesson%' => $lessonNumber ], 'email'),
                $link,
                $linkText,
                namesToErase: [
                    $student => $this->translator->trans('attendance.absent_without_note.child', domain: 'email')
                ]
            );

            $this->notificationService->notify($notification);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            StudentAbsentWithoutAnyNoteEvent::class => 'onStudentAbsentWithoutAnyNote'
        ];
    }

    public static function getSupportedRecipientUserTypes(): array {
        return [
            UserType::Teacher
        ];
    }

    public static function getKey(): string {
        return 'student_absent_without_note_teachers';
    }

    public static function getLabelKey(): string {
        return 'notifications.student_absent_without_note.label';
    }

    public static function getHelpKey(): string {
        return 'notifications.student_absent_without_note.help';
    }
}