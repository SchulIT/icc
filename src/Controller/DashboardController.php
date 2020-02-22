<?php

namespace App\Controller;

use App\Dashboard\DashboardViewHelper;
use App\Entity\User;
use App\Entity\UserType;
use App\Settings\DashboardSettings;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use App\View\Filter\UserTypeFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {

    use DateTimeHelperTrait;

    /**
     * @Route("/")
     * @Route("/dashboard", name="dashboard")
     */
    public function index(StudentFilter $studentFilter, TeacherFilter $teacherFilter, UserTypeFilter $userTypeFilter,
                          DashboardViewHelper $dashboardViewHelper, DateHelper $dateHelper, DashboardSettings $dashboardSettings,
                          ?int $studentId = null, ?string $teacherAcronym = null, ?string $userType = null, ?string $date = null) {
        /** @var User $user */
        $user = $this->getUser();
        $days = $this->getListOfNextDays($dateHelper, $dashboardSettings->getNumberOfAheadDaysForSubstitutions(), $dashboardSettings->skipWeekends());
        $selectedDate = $this->getCurrentDate($days, $date);

        $studentFilterView = $studentFilter->handle($studentId, $user);
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $user);
        $userTypeFilterView = $userTypeFilter->handle($userType, $user, false, UserType::Student(), [ UserType::Student(), UserType::Parent() ]);

        if($studentFilterView->getCurrentStudent() !== null) {
            $view = $dashboardViewHelper->createViewForStudentOrParent($studentFilterView->getCurrentStudent(), $selectedDate, $userTypeFilterView->getCurrentType());
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $view = $dashboardViewHelper->createViewForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate);
        } else {
            $view = $dashboardViewHelper->createViewForUser($user, $selectedDate);
        }

        dump($view);

        return $this->render('dashboard/index.html.twig', [
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'view' => $view,
            'days' => $days,
            'selectedDate' => $selectedDate
        ]);
    }
}