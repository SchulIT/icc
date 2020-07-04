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
use App\Sorting\Sorter;
use App\Sorting\SubstitutionGradeGroupStrategy;
use App\Sorting\SubstitutionStrategy;
use App\Sorting\SubstitutionTeacherGroupStrategy;
use App\Timetable\TimetableWeekHelper;
use FervoEnumBundle\Generated\Form\DisplayTargetUserTypeType;
use SchoolIT\CommonBundle\Helper\DateHelper;
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

        $substitutions = $substitutionRepository->findAllByDate($today);
        if($display->getSubstitutionsTarget()->equals(DisplayTargetUserType::Students())) {
            $substitutionGroups = $grouper->group($substitutions, SubstitutionGradeStrategy::class);
            $sorter->sort($substitutionGroups, SubstitutionGradeGroupStrategy::class);
        } else if($display->getSubstitutionsTarget()->equals(DisplayTargetUserType::Teachers())) {
            $substitutionGroups = $grouper->group($substitutions, SubstitutionTeacherStrategy::class);
            $sorter->sort($substitutionGroups, SubstitutionTeacherGroupStrategy::class);
        }

        $sorter->sortGroupItems($substitutionGroups, SubstitutionStrategy::class);

        return $this->render('display/show.html.twig', [
            'display' => $display,
            'week' => $currentWeek,
            'infotexts' => $infotextRepository->findAllByDate($today),
            'absent_studygroups' => $absenceRepository->findAllStudyGroups($today),
            'absent_teachers' => $absenceRepository->findAllTeachers($today),
            'groups' => $substitutionGroups
        ]);
    }
}