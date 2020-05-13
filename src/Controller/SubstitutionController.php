<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\Substitution;
use App\Entity\User;
use App\Grouping\Grouper;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Settings\SubstitutionSettings;
use App\Sorting\AbsentStudyGroupStrategy;
use App\Sorting\AbsentTeacherStrategy;
use App\Sorting\Sorter;
use App\Sorting\SubstitutionStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use App\View\Parameter\GroupByParameter;
use App\View\Parameter\ViewParameter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubstitutionController extends AbstractControllerWithMessages {

    use DateTimeHelperTrait;

    private const SectionKey = 'substitutions';

    /**
     * @Route("/substitutions", name="substitutions")
     */
    public function index(SubstitutionRepositoryInterface $substitutionRepository, InfotextRepositoryInterface $infotextRepository, AbsenceRepositoryInterface $absenceRepository,
                          StudentFilter $studentFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, GroupByParameter $groupByParameter, ViewParameter $viewParameter,
                          Grouper $grouper, Sorter $sorter, DateHelper $dateHelper, SubstitutionSettings $substitutionSettings, Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $days = $this->getListOfNextDays($dateHelper, $substitutionSettings->getNumberOfAheadDaysForSubstitutions(), $substitutionSettings->skipWeekends());
        $date = $request->query->get('date', null);
        $selectedDate = $this->getCurrentDate($days, $date);

        $groupBy = $request->query->get('group_by', null);
        $view = $request->query->get('view', null);

        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, $studentFilterView->getCurrentStudent() === null && $gradeFilterView->getCurrentGrade() === null);

        /** @var Substitution[] $substitutions */
        $substitutions = [ ];

        if($teacherFilterView->getCurrentTeacher() !== null) {
            $substitutions = $substitutionRepository->findAllForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate);
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $substitutions = $substitutionRepository->findAllForGrade($gradeFilterView->getCurrentGrade(), $selectedDate);
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $studyGroups = array_map(function(StudyGroupMembership $membership) {
                return $membership->getStudyGroup();
            }, $studentFilterView->getCurrentStudent()->getStudyGroupMemberships()->toArray());

            $substitutions = $substitutionRepository->findAllForStudyGroups($studyGroups, $selectedDate);
        } else {
            $substitutions = $substitutionRepository->findAllByDate($selectedDate);
        }

        $groupingClass = $groupByParameter->getGroupingStrategyClassName($groupBy, $user, static::SectionKey);
        $groups = $grouper->group($substitutions, $groupingClass);

        $sortingClass = $groupByParameter->getSortingStrategyClassName($groupingClass);

        $sorter->sort($groups, $sortingClass);
        $sorter->sortGroupItems($groups, SubstitutionStrategy::class);

        $viewType = $viewParameter->getViewType($view, $user, static::SectionKey);

        $absentTeachers = [ ];
        $absentStudyGroups = [ ];

        if($substitutionSettings->areAbsencesVisibleFor($user->getUserType())) {
            $absentTeachers = $absenceRepository->findAllTeachers($selectedDate);
            $absentStudyGroups = $absenceRepository->findAllStudyGroups($selectedDate);

            $sorter->sort($absentTeachers, AbsentTeacherStrategy::class);
            $sorter->sort($absentStudyGroups, AbsentStudyGroupStrategy::class);
        }

        return $this->renderWithMessages('substitutions/index.html.twig', [
            'infotexts' => $infotextRepository->findAllByDate($selectedDate),
            'groups' => $groups,
            'days' => $days,
            'selectedDate' => $selectedDate,
            'studentFilter' => $studentFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'view' => $viewType,
            'groupBy' => $groupByParameter->getGroupingStrategyKey($groupingClass),
            'absentTeachers' => $absentTeachers,
            'absentStudyGroups' => $absentStudyGroups
        ]);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Substitutions();
    }
}