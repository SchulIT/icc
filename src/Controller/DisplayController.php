<?php

namespace App\Controller;

use App\Display\DisplayHelper;
use App\Entity\Display;
use App\Entity\DisplayTargetUserType;
use App\Grouping\Grouper;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Sorting\AppointmentStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetableWeekHelper;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/display")
 */
class DisplayController extends AbstractController {

    /**
     * @Route("/{uuid}", name="show_display")
     */
    public function show(Display $display, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository,
                         SubstitutionRepositoryInterface $substitutionRepository, AppointmentRepositoryInterface $appointmentRepository,
                         TimetableWeekHelper $weekHelper, DateHelper $dateHelper, Grouper $grouper, Sorter $sorter, DisplayHelper $displayHelper) {
        $today = $dateHelper->getToday();
        $appointments = [ ];
        $currentWeek = $weekHelper->getTimetableWeek($today);
        $groups = [ ];

        if($display->getTargetUserType()->equals(DisplayTargetUserType::Students())) {
            $groups = $displayHelper->getStudentsItems($today);

            $appointments = $appointmentRepository->findAllForAllStudents($today);
        } else if($display->getTargetUserType()->equals(DisplayTargetUserType::Teachers())) {
            $groups = $displayHelper->getTeachersItems($today);

            $appointments = $appointmentRepository->findAll([], null, $today);
        }

        $sorter->sort($appointments, AppointmentStrategy::class);

        return $this->render('display/show.html.twig', [
            'display' => $display,
            'week' => $currentWeek,
            'infotexts' => $infotextRepository->findAllByDate($today),
            'absent_studygroups' => $absenceRepository->findAllStudyGroups($today),
            'absent_teachers' => $absenceRepository->findAllTeachers($today),
            'groups' => $groups,
            'appointments' => $appointments,
        ]);
    }
}