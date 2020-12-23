<?php

namespace App\Controller;

use App\Entity\Display;
use App\Entity\DisplayTargetUserType;
use App\Grouping\Grouper;
use App\Grouping\SubstitutionGradeStrategy;
use App\Grouping\SubstitutionTeacherStrategy;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Sorting\AppointmentStrategy;
use App\Sorting\Sorter;
use App\Sorting\SubstitutionGradeGroupStrategy;
use App\Sorting\SubstitutionStrategy;
use App\Sorting\SubstitutionTeacherGroupStrategy;
use App\Timetable\TimetableWeekHelper;
use FervoEnumBundle\Generated\Form\DisplayTargetUserTypeType;
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
                         TimetableWeekHelper $weekHelper, DateHelper $dateHelper, Grouper $grouper, Sorter $sorter) {
        $today = $dateHelper->getToday();
        $appointments = [ ];
        $currentWeek = $weekHelper->getTimetableWeek($today);


        if($display->getTargetUserType()->equals(DisplayTargetUserType::Students())) {
            $substitutions = $substitutionRepository->findAllByDate($today, true);
            $substitutionGroups = $grouper->group($substitutions, SubstitutionGradeStrategy::class);
            $sorter->sort($substitutionGroups, SubstitutionGradeGroupStrategy::class);

            $appointments = $appointmentRepository->findAllForAllStudents($today);
        } else if($display->getTargetUserType()->equals(DisplayTargetUserType::Teachers())) {
            $substitutions = $substitutionRepository->findAllByDate($today, false);
            $substitutionGroups = $grouper->group($substitutions, SubstitutionTeacherStrategy::class);
            $sorter->sort($substitutionGroups, SubstitutionTeacherGroupStrategy::class);

            $appointments = $appointmentRepository->findAll([], null, $today);
        }

        $sorter->sortGroupItems($substitutionGroups, SubstitutionStrategy::class);
        $sorter->sort($appointments, AppointmentStrategy::class);

        $totalNumberOfSubstitutions = 0;

        foreach($substitutionGroups as $group) {
            $totalNumberOfSubstitutions += count($group->getSubstitutions());
        }

        return $this->render('display/show.html.twig', [
            'display' => $display,
            'week' => $currentWeek,
            'infotexts' => $infotextRepository->findAllByDate($today),
            'absent_studygroups' => $absenceRepository->findAllStudyGroups($today),
            'absent_teachers' => $absenceRepository->findAllTeachers($today),
            'groups' => $substitutionGroups,
            'appointments' => $appointments,
            'total_substitutions' => $totalNumberOfSubstitutions
        ]);
    }
}