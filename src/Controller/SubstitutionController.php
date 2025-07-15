<?php

namespace App\Controller;

use App\Sorting\AbsentRoomStrategy;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\Substitution;
use App\Entity\User;
use App\Grouping\Grouper;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Settings\DashboardSettings;
use App\Settings\SubstitutionSettings;
use App\Sorting\AbsentStudyGroupStrategy;
use App\Sorting\AbsentTeacherStrategy;
use App\Sorting\Sorter;
use App\Sorting\SubstitutionStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\SectionFilterView;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use App\View\Parameter\GroupByParameter;
use App\View\Parameter\ViewParameter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SubstitutionController extends AbstractControllerWithMessages {

    use DateTimeHelperTrait;

    private const string SectionKey = 'substitutions';

    #[Route(path: '/substitutions', name: 'substitutions')]
    public function index(SubstitutionRepositoryInterface $substitutionRepository, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository,
                          StudentFilter $studentFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, GroupByParameter $groupByParameter, ViewParameter $viewParameter,
                          Grouper $grouper, Sorter $sorter, DateHelper $dateHelper, DashboardSettings $dashboardSettings, SubstitutionSettings $substitutionSettings,
                          ImportDateTypeRepositoryInterface $importDateTypeRepository, SectionRepositoryInterface $sectionRepository, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();
        $days = $this->getListOfNextDays($dateHelper, $substitutionSettings->getNumberOfAheadDaysForSubstitutions(), $substitutionSettings->skipWeekends(), $this->getTodayOrNextDay($dateHelper, $dashboardSettings->getNextDayThresholdTime()));
        $date = $request->query->get('date', null);
        $selectedDate = $this->getCurrentDate($days, $date);

        $groupBy = $request->query->get('group_by', null);

        $section = $sectionRepository->findOneByDate($selectedDate);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $section, $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $section, $user, $gradeFilterView->getCurrentGrade() === null);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $section, $user, false);

        /** @var Substitution[] $substitutions */
        $substitutions = [ ];
        /** @var int[] $counts Substitution counts for the upcoming days (idx: 0 -> first date, idx: 1 -> second date etc.) - TODO: improve this with a proper data structure! */
        $counts = [ ];

        if($teacherFilterView->getCurrentTeacher() !== null) {
            $substitutions = $substitutionRepository->findAllForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate);

            for($idx = 0; $idx < count($days); $idx++) {
                $counts[$idx] = $substitutionRepository->countAllForTeacher($teacherFilterView->getCurrentTeacher(), $days[$idx]);
            }
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $substitutions = $substitutionRepository->findAllForGrade($gradeFilterView->getCurrentGrade(), $selectedDate);

            for($idx = 0; $idx < count($days); $idx++) {
                $counts[$idx] = $substitutionRepository->countAllForGrade($gradeFilterView->getCurrentGrade(), $days[$idx]);
            }
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $studyGroups = array_map(fn(StudyGroupMembership $membership) => $membership->getStudyGroup(), $studentFilterView->getCurrentStudent()->getStudyGroupMemberships()->toArray());

            $substitutions = $substitutionRepository->findAllForStudyGroups($studyGroups, $selectedDate);

            for($idx = 0; $idx < count($days); $idx++) {
                $counts[$idx] = $substitutionRepository->countAllForStudyGroups($studyGroups, $days[$idx]);
            }
        } else {
            $substitutions = $substitutionRepository->findAllByDate($selectedDate);

            for($idx = 0; $idx < count($days); $idx++) {
                $counts[$idx] = $substitutionRepository->countAllByDate($days[$idx]);
            }
        }

        $groupingClass = $groupByParameter->getGroupingStrategyClassName($groupBy, $user, self::SectionKey);
        $groups = $grouper->group($substitutions, $groupingClass);

        $sortingClass = $groupByParameter->getSortingStrategyClassName($groupingClass);

        $sorter->sort($groups, $sortingClass);
        $sorter->sortGroupItems($groups, SubstitutionStrategy::class);

        $absentTeachers = [ ];
        $absentStudyGroups = [ ];
        $absentRooms = [ ];

        if($substitutionSettings->areAbsencesVisibleFor($user->getUserType())) {
            $absentTeachers = $absenceRepository->findAllTeachers($selectedDate);
            $absentStudyGroups = $absenceRepository->findAllStudyGroups($selectedDate);
            $absentRooms = $absenceRepository->findAllRooms($selectedDate);

            $sorter->sort($absentTeachers, AbsentTeacherStrategy::class);
            $sorter->sort($absentStudyGroups, AbsentStudyGroupStrategy::class);
            $sorter->sort($absentRooms, AbsentRoomStrategy::class);
        }

        return $this->renderWithMessages('substitutions/table.html.twig', [
            'infotexts' => $infotextRepository->findAllByDate($selectedDate),
            'groups' => $groups,
            'days' => $days,
            'selectedDate' => $selectedDate,
            'studentFilter' => $studentFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'groupBy' => $groupByParameter->getGroupingStrategyKey($groupingClass),
            'canGroup' => $groupByParameter->canGroup($user),
            'absentTeachers' => $absentTeachers,
            'absentStudyGroups' => $absentStudyGroups,
            'absentRooms' => $absentRooms,
            'counts' => $counts,
            'skipWeekends' => $substitutionSettings->skipWeekends(),
            'last_import' => $importDateTypeRepository->findOneByEntityClass(Substitution::class)
        ]);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Substitutions;
    }
}