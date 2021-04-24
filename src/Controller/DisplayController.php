<?php

namespace App\Controller;

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
use App\Timetable\TimetableWeekHelper;
use DateTime;
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
                         TimetableWeekHelper $weekHelper, DateHelper $dateHelper, Grouper $grouper, Sorter $sorter, DisplayHelper $displayHelper, ImportDateTypeRepositoryInterface  $importDateTymeRepository) {
        $dateHelper->setToday(new DateTime('2021-04-23'));

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

        $itemsCount = 0;

        foreach($groups as $group) {
            $itemsCount += count($group->getItems());
        }

        return $this->render('display/two_column_bottom.html.twig', [
            'display' => $display,
            'week' => $currentWeek,
            'infotexts' => $infotextRepository->findAllByDate($today),
            'absent_studygroups' => $absenceRepository->findAllStudyGroups($today),
            'absent_teachers' => $absenceRepository->findAllTeachers($today),
            'groups' => $groups,
            'appointments' => $appointments,
            'count' => $itemsCount,
            'last_update' => $importDateTymeRepository->findOneByEntityClass(Substitution::class),
            'day' => $today,
            'is_teachersview' => $display->getTargetUserType()->equals(DisplayTargetUserType::Teachers())
        ]);
    }
}