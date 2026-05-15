<?php

namespace App\Display\Controller;

use App\Common\Repository\TeacherRepositoryInterface;
use App\Framework\Controller\AbstractController;
use App\Timetable\Repository\TimetableWeekRepositoryInterface;
use App\Tools\CountdownCalculator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Display\DisplayHelper;
use App\Display\Entity\Display;
use App\Display\Entity\DisplayTargetUserType;
use App\Substitution\Entity\Substitution;
use App\Framework\Grouping\Grouper;
use App\Substitution\Repository\AbsenceRepositoryInterface;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Framework\Import\Repository\ImportDateTypeRepositoryInterface;
use App\Substitution\Repository\InfotextRepositoryInterface;
use App\Substitution\Repository\SubstitutionRepositoryInterface;
use App\Appointment\Sorting\AppointmentStrategy;
use App\Framework\Sorting\Sorter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/display')]
class DisplayController extends AbstractController {

    #[Route(path: '/{uuid}', name: 'show_display')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Display $display, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository,
                         TimetableWeekRepositoryInterface $timetableWeekRepository, AppointmentRepositoryInterface $appointmentRepository,
                         DateHelper $dateHelper, Grouper $grouper, Sorter $sorter, DisplayHelper $displayHelper,
                         ImportDateTypeRepositoryInterface  $importDateTymeRepository, TeacherRepositoryInterface $teacherRepository, CountdownCalculator $countdownCalculator): Response {
        $today = $dateHelper->getToday();
        $appointments = [ ];
        $groups = [ ];

        if($display->getTargetUserType() === DisplayTargetUserType::Students) {
            $groups = $displayHelper->getStudentsItems($today);

            $appointments = $appointmentRepository->findAllForAllStudents($today);
        } else if($display->getTargetUserType() === DisplayTargetUserType::Teachers) {
            $groups = $displayHelper->getTeachersItems($today);

            $appointments = $appointmentRepository->findAll([], null, $today);
        }

        $sorter->sort($appointments, AppointmentStrategy::class);

        $week = $timetableWeekRepository->findOneByWeekNumber((int)$today->format('W'));

        $birthdays = [ ];
        if($display->getTargetUserType() === DisplayTargetUserType::Teachers) {
            $birthdays = $teacherRepository->findAllByBirthday($today);
        }

        $countdownDays = null;

        if($display->getCountdownDate() !== null) {
            $countdownDays = $countdownCalculator->computeSchoolDaysUntis($display->getCountdownDate());
        }

        return $this->render('display/display.html.twig', [
            'display' => $display,
            'infotexts' => $infotextRepository->findAllByDate($today),
            'absent_studygroups' => $absenceRepository->findAllStudyGroups($today),
            'absent_teachers' => $absenceRepository->findAllTeachers($today),
            'absent_rooms' => $absenceRepository->findAllRooms($today),
            'groups' => $groups,
            'appointments' => $appointments,
            'last_update' => $importDateTymeRepository->findOneByEntityClass(Substitution::class),
            'day' => $today,
            'week' => $week,
            'is_teachersview' => $display->getTargetUserType() === DisplayTargetUserType::Teachers,
            'teacher_birthdays' => $birthdays,
            'countdown' => $countdownDays
        ]);
    }
}