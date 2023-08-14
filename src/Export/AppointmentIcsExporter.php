<?php

namespace App\Export;

use Jsvrcek\ICS\Exception\CalendarEventException;
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

    public function __construct(private AppointmentRepositoryInterface $appointmentsRepository, private IcsHelper $icsHelper, private StudyGroupsGradeStringConverter $studyGroupsConverter, private TeacherStringConverter $teacherConverter, private AuthorizationCheckerInterface $authorizationChecker, private TranslatorInterface $translator)
    {
    }

    /**
     * @return CalendarEvent[]
     */
    private function getEvents(User $user): array {
        $isStudentOrParent = $user->isStudentOrParent();
        $isTeacher = $user->isTeacher();

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
     * @throws CalendarEventException
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

        /*if($appointment->getOrganizers()->count() > 0 || !empty($appointment->getExternalOrganizers())) {
            $organizers = $appointment->getOrganizers()->map(fn(Teacher $teacher) => $this->teacherConverter->convert($teacher))->toArray();
            $organizers[] = $appointment->getExternalOrganizers();

            $description[] = $this->translator->trans('plans.appointments.export.organizers', [
                '%count%' => count($organizers),
                '%name%' => implode(', ', $organizers)
            ]);
        }*/

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
