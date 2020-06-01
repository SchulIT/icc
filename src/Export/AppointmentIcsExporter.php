<?php

namespace App\Export;

use App\Converter\StudyGroupsGradeStringConverter;
use App\Converter\TeacherStringConverter;
use App\Entity\Appointment;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Ics\IcsHelper;
use App\Repository\AppointmentRepositoryInterface;
use App\Security\Voter\AppointmentVoter;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppointmentIcsExporter {

    private $appointmentsRepository;
    private $icsHelper;
    private $studyGroupsConverter;
    private $teacherConverter;
    private $authorizationChecker;
    private $translator;

    public function __construct(AppointmentRepositoryInterface $appointmentsRepository, IcsHelper $icsHelper,
                                StudyGroupsGradeStringConverter $studyGroupsConverter, TeacherStringConverter $teacherConverter,
                                AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator) {
        $this->appointmentsRepository = $appointmentsRepository;
        $this->icsHelper = $icsHelper;
        $this->studyGroupsConverter = $studyGroupsConverter;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->teacherConverter = $teacherConverter;
    }

    /**
     * @param User $user
     * @return CalendarEvent[]
     */
    private function getEvents(User $user): array {
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());
        $isTeacher = $user->getUserType()->equals(UserType::Teacher());

        $appointments = [ ];

        if($isStudentOrParent) {
            $appointments = $this->appointmentsRepository->findAllForStudents($user->getStudents()->toArray());
        } else if($isTeacher) {
            $appointments = $this->appointmentsRepository->findAllForTeacher($user->getTeacher());
        } else {
            $appointments = $this->appointmentsRepository->findAll();
        }

        $items = [ ];

        foreach($appointments as $appointment) {
            if($this->authorizationChecker->isGranted(AppointmentVoter::View, $appointment)) {
                $items[] = $this->getEvent($appointment);
            }
        }

        return $items;
    }

    /**
     * @param Appointment $appointment
     * @return CalendarEvent
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     */
    private function getEvent(Appointment $appointment): CalendarEvent {
        $event = (new CalendarEvent())
            ->setUid(sprintf('appointment-%d', $appointment->getId()))
            ->setStart($appointment->getStart())
            ->setEnd($appointment->getEnd())
            ->setAllDay($appointment->isAllDay())
            ->setSummary($this->makeSubject($appointment))
            ->setDescription($this->makeDescription($appointment));

        foreach($appointment->getOrganizers() as $organizer) {
            if(!empty($organizer->getEmail())) {
                $attendee = (new Attendee(new Formatter()))
                    ->setName($this->teacherConverter->convert($organizer))
                    ->setValue($organizer->getEmail());

                $event->addAttendee($attendee);
            }
        }

        return $event;
    }

    private function makeSubject(Appointment $appointment): string {
        if($appointment->getStudyGroups()->count() > 0) {
            return sprintf("%s [%s]", $appointment->getTitle(), $this->studyGroupsConverter->convert($appointment->getStudyGroups()));
        }

        return $appointment->getTitle();
    }

    private function makeDescription(Appointment $appointment): string {
        $description = [ ];

        if(!empty($appointment->getContent())) {
            $description[] = $appointment->getContent();
            $description[] = ''; // empty line
        }

        if($appointment->getCategory() !== null) {
            $description[] = $this->translator->trans('plans.appointments.export.category', ['%name%' => $appointment->getCategory()->getName() ]);
        }

        if($appointment->getOrganizers()->count() > 0 || !empty($appointment->getExternalOrganizers())) {
            $organizers = $appointment->getOrganizers()->map(function(Teacher $teacher) {
                return $this->teacherConverter->convert($teacher);
            })->toArray();
            $organizers[] = $appointment->getExternalOrganizers();

            $description[] = $this->translator->trans('plans.appointments.export.organizers', [
                '%count%' => count($organizers),
                '%name%' => implode(', ', $organizers)
            ]);
        }

        return implode('\n', $description);
    }

    public function getIcsResponse(User $user): Response {
        $events = $this->getEvents($user);

        return $this->icsHelper->getIcsResponse(
            $this->translator->trans('plans.appointments.export.name'),
            $this->translator->trans('plans.appointments.export.description'),
            $events,
            $this->translator->trans('plans.appointments.export.download.filename')
        );
    }

}