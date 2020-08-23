<?php

namespace App\Controller;

use App\Entity\IcsAccessToken;
use App\Entity\IcsAccessTokenType;
use App\Entity\Exam;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\User;
use App\Entity\UserType;
use App\Export\ExamIcsExporter;
use App\Form\IcsAccessTokenType as DeviceTokenTypeForm;
use App\Grouping\ExamWeekGroup;
use App\Grouping\Grouper;
use App\Grouping\StudentStudyGroupGroup;
use App\Grouping\WeekOfYear;
use App\Message\DismissedMessagesHelper;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Security\IcsAccessToken\IcsAccessTokenManager;
use App\Security\Voter\ExamVoter;
use App\Settings\ExamSettings;
use App\Sorting\ExamDateLessonStrategy as ExamDateSortingStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Sorting\StudentStudyGroupGroupStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudyGroupFilter;
use App\View\Filter\TeacherFilter;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/exams")
 */
class ExamController extends AbstractControllerWithMessages {

    private $grouper;
    private $sorter;

    private $importDateTypeRepository;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper, ImportDateTypeRepositoryInterface $importDateTypeRepository,
                                DateHelper $dateHelper, Grouper $grouper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);

        $this->grouper = $grouper;
        $this->sorter = $sorter;
        $this->importDateTypeRepository = $importDateTypeRepository;
    }

    /**
     * @Route("", name="exams")
     */
    public function index(TeacherFilter $teacherFilter, StudentFilter $studentsFilter, GradeFilter $gradeFilter, StudyGroupFilter $studyGroupFilter,
                          ExamRepositoryInterface $examRepository, ExamSettings $examSettings, Request $request, DateHelper $dateHelper) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudentOrParent = $user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent());

        $studentFilterView = $studentsFilter->handle($request->query->get('student', null), $user);
        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group', null), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, $studentFilterView->getCurrentStudent() === null && $studyGroupFilterView->getCurrentStudyGroup() === null && $gradeFilterView->getCurrentGrade() === null);

        $isVisible = $examSettings->isVisibileFor($user->getUserType()) && $this->isVisibleForGrade($user, $examSettings);
        $isVisibleAdmin = false;

        $week = $request->query->has('week') ? $request->query->getInt('week') : null;
        $year = $request->query->has('year') ? $request->query->getInt('year') : null;

        $groups = [ ];
        $currentGroup = null;
        $exams = [ ];

        if($isVisible === true || $this->isGranted('ROLE_EXAMS_CREATOR') || $this->isGranted('ROLE_EXAMS_ADMIN')) {
            if($isVisible === false) {
                $isVisibleAdmin = $this->isGranted('ROLE_EXAMS_CREATOR') || $this->isGranted('ROLE_EXAMS_ADMIN');
            }

            if ($studentFilterView->getCurrentStudent() !== null) {
                $groups = $this->computeGroups($examRepository->findAllDatesByStudents([$studentFilterView->getCurrentStudent()]));
                $currentGroup = $this->getCurrentGroup($groups, $year, $week, $dateHelper);

                $exams = $this->getExams($currentGroup, function (DateTime $dateTime) use ($studentFilterView, $examRepository) {
                    return $examRepository->findAllByStudents([$studentFilterView->getCurrentStudent()], $dateTime, true);
                });
            } else {
                if ($studyGroupFilterView->getCurrentStudyGroup() !== null) {
                    $groups = $this->computeGroups($examRepository->findAllDatesByStudyGroup($studyGroupFilterView->getCurrentStudyGroup()));
                    $currentGroup = $this->getCurrentGroup($groups, $year, $week, $dateHelper);

                    $exams = $this->getExams($currentGroup, function (DateTime $dateTime) use ($studyGroupFilterView, $examRepository) {
                        return $examRepository->findAllByStudyGroup($studyGroupFilterView->getCurrentStudyGroup(), $dateTime, true,);
                    });
                } else {
                    if ($gradeFilterView->getCurrentGrade() !== null) {
                        $groups = $this->computeGroups($examRepository->findAllDatesByGrade($gradeFilterView->getCurrentGrade()));
                        $currentGroup = $this->getCurrentGroup($groups, $year, $week, $dateHelper);

                        $exams = $this->getExams($currentGroup, function (DateTime $dateTime) use ($gradeFilterView, $examRepository) {
                            return $examRepository->findAllByGrade($gradeFilterView->getCurrentGrade(), $dateTime, true);
                        });
                    } else {
                        if ($isStudentOrParent === false) {
                            if ($teacherFilterView->getCurrentTeacher() !== null) {
                                $groups = $this->computeGroups($examRepository->findAllDatesByTeacher($teacherFilterView->getCurrentTeacher()));
                                $currentGroup = $this->getCurrentGroup($groups, $year, $week, $dateHelper);

                                $exams = $this->getExams($currentGroup, function (DateTime $dateTime) use ($teacherFilterView, $examRepository) {
                                    return $examRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $dateTime, true);
                                });
                            } else {
                                $groups = $this->computeGroups($examRepository->findAllDates());
                                $currentGroup = $this->getCurrentGroup($groups, $year, $week, $dateHelper);

                                $exams = $this->getExams($currentGroup, function (DateTime $dateTime) use ($examRepository) {
                                    return $examRepository->findAll($dateTime, true);
                                });
                            }
                        }
                    }
                }
            }

            $exams = array_filter($exams, function (Exam $exam) {
                return $this->isGranted(ExamVoter::Show, $exam);
            });
        }

        $previousGroup = null;
        $nextGroup = null;

        /** @var ExamWeekGroup $group */
        for($idx = 0; $idx < count($groups); $idx++) {
            $group = $groups[$idx];

            if($group === $currentGroup) {
                $previousGroup = $examWeekGroups[$idx - 1] ?? null;
                $nextGroup = $examWeekGroups[$idx + 1] ?? null;
            }
        }

        $this->sorter->sort($exams, ExamDateSortingStrategy::class);

        return $this->renderWithMessages('exams/index.html.twig', [
            'examWeekGroups' => $groups,
            'studentFilter' => $studentFilterView,
            'teacherFilter' => $teacherFilterView,
            'gradeFilter' => $gradeFilterView,
            'studyGroupFilter' => $studyGroupFilterView,
            'isVisible' => $isVisible,
            'isVisibleAdmin' => $isVisibleAdmin,
            'exams' => $exams,
            'currentGroup' => $currentGroup,
            'nextGroup' => $nextGroup,
            'previousGroup' => $previousGroup,
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }

    private function isVisibleForGrade(User $user, ExamSettings $examSettings) {
        $visibleGradeIds = $examSettings->getVisibleGradeIds();
        $gradeIds = [ ];

        /** @var Student $student */
        foreach($user->getStudents() as $student) {
            $gradeIds[] = $student->getGrade()->getId();
        }

        return count(array_intersect($visibleGradeIds, $gradeIds)) > 0;
    }

    private function getExams(?ExamWeekGroup $group, \Closure $repositoryCall) {
        if($group === null) {
            return [];
        }

        $date = clone $group->getWeekOfYear()->getFirstDay();
        $exams = [ ];

        while($date <= $group->getWeekOfYear()->getLastDay()) {
            $exams = array_merge($exams, $repositoryCall($date));

            $date = $date->modify('+1 day');
        }

        return $exams;
    }

    /**
     * @param ExamWeekGroup[] $groups
     * @param int|null $year
     * @param int|null $weekNumber
     * @param DateHelper $dateHelper
     * @return ExamWeekGroup|null
     */
    private function getCurrentGroup(array $groups, ?int $year, ?int $weekNumber, DateHelper $dateHelper): ?ExamWeekGroup {
        if ($year === null || $weekNumber === null) {
            $today = $dateHelper->getToday();
            $weekNumber = (int)$today->format('W');
            $year = (int)$today->format('Y');
        }

        $currentGroup = null;

        foreach ($groups as $group) {
            if ($group->getWeekOfYear()->getYear() >= $year && $group->getWeekOfYear()->getWeekNumber() >= $weekNumber) {
                $currentGroup = $group;

                if ($group->getWeekOfYear()->getYear() === $year && $group->getWeekOfYear()->getWeekNumber() === $weekNumber) {
                    break;
                }
            }
        }

        return $currentGroup;
    }

    private function computeGroups(array $examInfo) {
        $groups = [ ];

        foreach($examInfo as $info) {
            $date = new DateTime($info['date']);
            $count = intval($info['count']);

            $weekNumber = (int)$date->format('W');
            $year = (int)$date->format('Y');

            $key = sprintf('%d-%d', $year, $weekNumber);

            if(!array_key_exists($key, $groups)) {
                $groups[$key] = new ExamWeekGroup(new WeekOfYear($year, $weekNumber));
            }

            // Add fake counter
            for($i = 0; $i < $count; $i++) {
                $groups[$key]->addItem(new Exam());
            }
        }

        return array_values($groups);
    }

    /**
     * @Route("/export", name="exams_export")
     */
    public function export(Request $request, IcsAccessTokenManager $manager) {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new IcsAccessToken())
            ->setType(IcsAccessTokenType::Exams())
            ->setUser($user);

        $form = $this->createForm(DeviceTokenTypeForm::class, $deviceToken);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $deviceToken = $manager->persistToken($deviceToken);
        }

        return $this->renderWithMessages('exams/export.html.twig', [
            'form' => $form->createView(),
            'token' => $deviceToken
        ]);
    }

    /**
     * @Route("/ics/download", name="exams_ics")
     * @Route("/ics/download/{token}", name="exams_ics_token")
     */
    public function ics(ExamIcsExporter $exporter) {
        /** @var User $user */
        $user = $this->getUser();

        return $exporter->getIcsResponse($user);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Exams();
    }

    /**
     * @Route("/{uuid}", name="show_exam", requirements={"id": "\d+"})
     */
    public function show(Exam $exam) {
        $this->denyAccessUnlessGranted(ExamVoter::Show, $exam);

        $studyGroups = [ ];
        /** @var Student[] $students */
        $students = $exam->getStudents();

        $studentsWithoutStudyGroup = [ ];

        /** @var StudyGroup[] $studyGroups */
        $studyGroups = $exam->getTuitions()
            ->map(function(Tuition $tuition) {
                return $tuition->getStudyGroup();
            })->toArray();

        $groups = [ ];

        foreach($studyGroups as $studyGroup) {
            $groups[$studyGroup->getId()] = new StudentStudyGroupGroup($studyGroup);
        }

        foreach($students as $student) {
            /** @var StudyGroupMembership $membership */
            foreach($student->getStudyGroupMemberships() as $membership) {
                $id = $membership->getStudyGroup()->getId();
                if(isset($groups[$id])) {
                    $groups[$id]->addItem($student);
                    continue 2;
                }
            }

            $studentsWithoutStudyGroup[] = $student;
        }

        $this->sorter->sort($groups, StudentStudyGroupGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, StudentStrategy::class);

        $this->sorter->sort($studentsWithoutStudyGroup, StudentStrategy::class);

        return $this->renderWithMessages('exams/details.html.twig', [
            'exam' => $exam,
            'groups' => $groups,
            'studentsWithoutStudyGroup' => $studentsWithoutStudyGroup,
            'studyGroups' => $studyGroups,
            'last_import' => $this->importDateTypeRepository->findOneByEntityClass(Exam::class)
        ]);
    }
}