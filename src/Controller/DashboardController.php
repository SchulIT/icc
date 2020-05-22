<?php

namespace App\Controller;

use App\Dashboard\DashboardViewHelper;
use App\Dashboard\DashboardViewCollapseHelper;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepositoryInterface;
use App\Settings\SubstitutionSettings;
use App\Settings\TimetableSettings;
use App\Utils\EnumArrayUtils;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use App\View\Filter\UserTypeFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {

    private const DaysInFuture = 5;
    private const DaysInPast = 1;

    /**
     * @Route("/", name="index")
     */
    public function index() {
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(StudentFilter $studentFilter, TeacherFilter $teacherFilter, UserTypeFilter $userTypeFilter,
                              DashboardViewHelper $dashboardViewHelper, DashboardViewCollapseHelper $dashboardViewMergeHelper,
                              DateHelper $dateHelper, TimetableSettings $timetableSettings, UserRepositoryInterface $userRepository, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        if($request->isMethod('POST')) {
            $showTimes = $request->request->getBoolean('show_times', false);
            $user->setData('dashboard.show_times', $showTimes);
            $userRepository->persist($user);

            return $this->redirectToRoute('dashboard', $request->query->all());
        }

        $selectedDate = null;
        try {
            $selectedDate = new \DateTime($request->query->get('date', null));
            $selectedDate->setTime(0, 0, 0);
        } catch (\Exception $e) {
            $selectedDate = $dateHelper->getToday();
        }

        $days = $this->getListOfSurroundingDays($selectedDate, static::DaysInFuture, static::DaysInPast);

        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, $studentFilterView->getCurrentStudent() === null);
        $userTypeFilterView = $userTypeFilter->handle($request->query->get('user_type', null), $user, EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]), UserType::Student(), [ UserType::Student(), UserType::Parent() ]);

        if($studentFilterView->getCurrentStudent() !== null) {
            if($userTypeFilterView->getCurrentType() === null) {
                $userTypeFilterView->setCurrentType(UserType::Student());
            }
            $view = $dashboardViewHelper->createViewForStudentOrParent($studentFilterView->getCurrentStudent(), $selectedDate, $userTypeFilterView->getCurrentType());
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            $view = $dashboardViewHelper->createViewForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate);
        } else {
            $view = $dashboardViewHelper->createViewForUser($user, $selectedDate);
        }

        $startTimes = [ ];
        $endTimes = [ ];

        $showTimes = $user->getData('dashboard.show_times', true) === true;

        for ($lesson = 1; $lesson <= $timetableSettings->getMaxLessons(); $lesson++) {
            $startTimes[$lesson] = $showTimes ? $timetableSettings->getStart($lesson) : null;
            $endTimes[$lesson] = $showTimes ? $timetableSettings->getEnd($lesson) : null;
        }

        if($view !== null) {
            $dashboardViewMergeHelper->collapseView($view, $teacherFilterView->getCurrentTeacher());
        }

        $supervisionLabels = [ ];
        for($i = 1; $i <= $timetableSettings->getMaxLessons(); $i++) {
            $supervisionLabels[$i] = $timetableSettings->getDescriptionBeforeLesson($i);
        }

        return $this->render('dashboard/index.html.twig', [
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'view' => $view,
            'days' => $days,
            'selectedDate' => $selectedDate,
            'startTimes' => $startTimes,
            'endTimes' => $endTimes,
            'gradesWithCourseNames' => $timetableSettings->getGradeIdsWithCourseNames(),
            'supervisionLabels' => $supervisionLabels,
            'showTimes' => $showTimes
        ]);
    }

    /**
     * @param \DateTime $dateTime
     * @param int $daysInFuture
     * @param int $daysInPast
     * @return \DateTime[]
     */
    private function getListOfSurroundingDays(\DateTime $dateTime, int $daysInFuture, int $daysInPast): array {
        $days = [ ];

        for($i = $daysInPast; $i > 0; $i--) {
            $days[] = (clone $dateTime)->modify(sprintf('-%d days', $i));
        }

        $days[] = $dateTime;

        for($i = 1; $i <= $daysInFuture; $i++) {
            $days[] = (clone $dateTime)->modify(sprintf('+%d days', $i));
        }

        return $days;
    }
}