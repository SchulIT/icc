<?php

namespace App\Controller;

use App\Dashboard\DashboardViewHelper;
use App\Dashboard\DashboardViewCollapseHelper;
use App\Entity\Section;
use App\Entity\Substitution;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Settings\DashboardSettings;
use App\Settings\SubstitutionSettings;
use App\Settings\TimetableSettings;
use App\Utils\EnumArrayUtils;
use App\View\Filter\RoomFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use App\View\Filter\UserTypeFilter;
use DateTime;
use League\Csv\Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {

    use DateTimeHelperTrait;

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
    public function dashboard(StudentFilter $studentFilter, TeacherFilter $teacherFilter, UserTypeFilter $userTypeFilter, RoomFilter $roomFilter,
                              DashboardViewHelper $dashboardViewHelper, DashboardViewCollapseHelper $dashboardViewMergeHelper,
                              DateHelper $dateHelper, DashboardSettings $settings, TimetableSettings $timetableSettings, SectionRepositoryInterface $sectionRepository,
                              UserRepositoryInterface $userRepository, ImportDateTypeRepositoryInterface $importDateTypeRepository, Request $request) {
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

            while($settings->skipWeekends() && $selectedDate->format('N') > 5) {
                $selectedDate->modify('+1 day');
            }
        }

        $dateHasSection = true;
        $section = $this->getSectionForDate($selectedDate, $sectionRepository, $dateHasSection);

        $days = $this->getListOfSurroundingDays($selectedDate, $settings->getNumberFutureDays(), $settings->getNumberPastDays(), $settings->skipWeekends());

        $roomFilterView = $roomFilter->handle($request->query->get('room', null), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $section, $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $section, $user, $studentFilterView->getCurrentStudent() === null && $roomFilterView->getCurrentRoom() === null);
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
        } else if($roomFilterView->getCurrentRoom() !== null) {
            $view = $dashboardViewHelper->createViewForRoom($roomFilterView->getCurrentRoom(), $selectedDate);
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

        $template = 'dashboard/one_column.html.twig';

        if(count($view->getLessons()) > 0 && (count($view->getAppointments()) > 0 || count($view->getMessages()) > 0 || count($view->getPriorityMessages()) > 0)) {
            $template = 'dashboard/two_columns.html.twig';
        }

        return $this->render($template, [
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'roomFilter' => $roomFilterView,
            'view' => $view,
            'days' => $days,
            'selectedDate' => $selectedDate,
            'startTimes' => $startTimes,
            'endTimes' => $endTimes,
            'gradesWithCourseNames' => $timetableSettings->getGradeIdsWithCourseNames(),
            'supervisionLabels' => $supervisionLabels,
            'showTimes' => $showTimes,
            'includeGradeMessages' => $user->getData(static::IncludeGradeMessagesKey, false),
            'canIncludeGradeMessages' => $user->getTeacher() !== null,
            'last_import' => $importDateTypeRepository->findOneByEntityClass(Substitution::class),
            'settings' => $settings,
            'section' => $section,
            'dateHasSection' => $dateHasSection
        ]);
    }

    private function getSectionForDate(DateTime $dateTime, SectionRepositoryInterface $sectionRepository, bool &$dateHasSection): ?Section {
        $dateHasSection = true;
        $section = $sectionRepository->findOneByDate($dateTime);

        if($section === null) {
            $dateHasSection = false;
            $sections = $sectionRepository->findAll();

            if(count($sections) === 0) {
                return null;
            } else {
                return $sections[count($sections) - 1];
            }
        }

        return $section;
    }

    /**
     * @param \DateTime $dateTime
     * @param int $daysInFuture
     * @param int $daysInPast
     * @param bool $skipWeekends
     * @return \DateTime[]
     */
    private function getListOfSurroundingDays(\DateTime $dateTime, int $daysInFuture, int $daysInPast, bool $skipWeekends): array {
        $days = [ ];

        for($i = $daysInPast; $i > 0; $i--) {
            $day = (clone $dateTime)->modify(sprintf('-%d days', $i));

            if($skipWeekends === false || $day->format('N') < 6) {
                $days[] = $day;
            }
        }

        $days[] = $dateTime;

        for($i = 1; $i <= $daysInFuture; $i++) {
            $day = (clone $dateTime)->modify(sprintf('+%d days', $i));

            if($skipWeekends === false || $day->format('N') < 6) {
                $days[] = $day;
            }
        }

        return $days;
    }
}