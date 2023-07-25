<?php

namespace App\Controller;

use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Tools\CountdownCalculator;
use Symfony\Component\HttpFoundation\Response;
use App\Display\DisplayHelper;
use App\Entity\Display;
use App\Entity\DisplayTargetUserType;
use App\Entity\Substitution;
use App\Grouping\Grouper;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Sorting\AppointmentStrategy;
use App\Sorting\Sorter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/display')]
class DisplayController extends AbstractController {

    #[Route(path: '/{uuid}', name: 'show_display')]
    public function show(Display $display, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository,
                         TimetableWeekRepositoryInterface $timetableWeekRepository, AppointmentRepositoryInterface $appointmentRepository,
                         DateHelper $dateHelper, Grouper $grouper, Sorter $sorter, DisplayHelper $displayHelper,
                         ImportDateTypeRepositoryInterface  $importDateTymeRepository, TeacherRepositoryInterface $teacherRepository, CountdownCalculator $countdownCalculator): Response {
        $dateHelper->setToday(new DateTime('2023-06-01'));
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

        $itemsCount = 0;

        foreach($groups as $group) {
            $itemsCount += is_countable($group->getItems()) ? count($group->getItems()) : 0;
        }

        $week = $timetableWeekRepository->findOneByWeekNumber((int)$today->format('W'));

        $birthdays = [ ];
        if($display->getTargetUserType() === DisplayTargetUserType::Teachers) {
            $birthdays = $teacherRepository->findAllByBirthday($today);
        }

        $countdownDays = null;

        if($display->getCountdownDate() !== null) {
            $countdownDays = $countdownCalculator->computeSchoolDaysUntis($display->getCountdownDate());
        }

        return $this->render('display/two_column_bottom.html.twig', [
            'display' => $display,
            'infotexts' => $infotextRepository->findAllByDate($today),
            'absent_studygroups' => $absenceRepository->findAllStudyGroups($today),
            'absent_teachers' => $absenceRepository->findAllTeachers($today),
            'groups' => $groups,
            'appointments' => $appointments,
            'count' => $itemsCount,
            'last_update' => $importDateTymeRepository->findOneByEntityClass(Substitution::class),
            'day' => $today,
            'week' => $week,
            'is_teachersview' => $display->getTargetUserType() === DisplayTargetUserType::Teachers,
            'teacher_birthdays' => $birthdays,
            'countdown' => $countdownDays
        ]);
    }
}