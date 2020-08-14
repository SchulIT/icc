<?php

namespace App\Controller;

use App\Dashboard\DashboardViewHelper;
use App\Dashboard\DashboardViewCollapseHelper;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Settings\DashboardSettings;
use App\Settings\SubstitutionSettings;
use App\Settings\TimetableSettings;
use App\Utils\EnumArrayUtils;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use App\View\Filter\UserTypeFilter;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {

    use DateTimeHelperTrait;

    private const DaysInFuture = 5;
    private const DaysInPast = 1;

    private const ShowTimesKey = 'dashboard.show_times';
    private const IncludeGradeMessagesKey = 'dashboard.include_grade_messages';

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
                              DateHelper $dateHelper, DashboardSettings $settings, TimetableSettings $timetableSettings,
                              UserRepositoryInterface $userRepository, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        if($request->isMethod('POST')) {
            $showTimes = $request->request->getBoolean('show_times', false);
            $user->setData(static::ShowTimesKey, $showTimes);

            $includeGradeMessages = $request->request->getBoolean('include_grade_messages', false);
            $user->setData(static::IncludeGradeMessagesKey, $includeGradeMessages);

            $userRepository->persist($user);

            return $this->redirectToRoute('dashboard', $request->query->all());
        }

        $selectedDate = null;
        try {
            if($request->query->has('date')) {
                $selectedDate = new \DateTime($request->query->get('date', null));
                $selectedDate->setTime(0, 0, 0);
            }
        } catch (\Exception $e) {
            $selectedDate = null;
        }

        if($selectedDate === null) {
            $selectedDate = $this->getTodayOrNextDay($dateHelper, $settings->getNextDayThresholdTime());
        }

        $days = $this->getListOfSurroundingDays($selectedDate, static::DaysInFuture, static::DaysInPast);

        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, $studentFilterView->getCurrentStudent() === null);
        $userTypeFilterView = $userTypeFilter->handle($request->query->get('user_type', null), $user, EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]), UserType::Student(), [ UserType::Student(), UserType::Parent() ]);

        $includeGradeMessages = $user->getData(static::IncludeGradeMessagesKey, false);

        if($studentFilterView->getCurrentStudent() !== null) {
            if($userTypeFilterView->getCurrentType() === null) {
                $userTypeFilterView->setCurrentType(UserType::Student());
            }
            $view = $dashboardViewHelper->createViewForStudentOrParent($studentFilterView->getCurrentStudent(), $selectedDate, $userTypeFilterView->getCurrentType());
        } else if($teacherFilterView->getCurrentTeacher() !== null) {
            if($user->getTeacher() === null || $user->getTeacher()->getId() !== $teacherFilterView->getCurrentTeacher()->getId()) {
                // Only include grade messages if the current user is the selected user in the teacher filter.
                $includeGradeMessages = false;
            }

            $view = $dashboardViewHelper->createViewForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate, $includeGradeMessages);
        } else {
            $view = $dashboardViewHelper->createViewForUser($user, $selectedDate);
        }

        $startTimes = [ ];
        $endTimes = [ ];

        $showTimes = $user->getData(static::ShowTimesKey, true) === true;

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
            'showTimes' => $showTimes,
            'includeGradeMessages' => $user->getData(static::IncludeGradeMessagesKey, false),
            'canIncludeGradeMessages' => $user->getTeacher() !== null
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