<?php

namespace App\Controller;

use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Form\SickNoteType;
use App\Grouping\Grouper;
use App\Grouping\SickNoteGradeGroup;
use App\Grouping\SickNoteGradeStrategy;
use App\Grouping\SickNoteTuitionGroup;
use App\Repository\SickNoteRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\SickNoteVoter;
use App\Settings\SickNoteSettings;
use App\Settings\TimetableSettings;
use App\SickNote\SickNote;
use App\SickNote\SickNoteSender;
use App\Sorting\SickNoteGradeGroupStrategy;
use App\Sorting\SickNoteStrategy;
use App\Sorting\SickNoteTuitionGroupStrategy;
use App\Sorting\Sorter;
use App\Timetable\TimetableTimeHelper;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\TeacherFilter;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SickNoteController extends AbstractController {

    use DateTimeHelperTrait;

    /**
     * @Route("/sick_note", name="sick_note")
     */
    public function add(Request $request, SickNoteSender $sender, SickNoteSettings $settings,
                        SickNoteRepositoryInterface $repository, StudentRepositoryInterface $studentRepository,
                        TimetableTimeHelper $timeHelper, TimetableSettings $timetableSettings, DateHelper $dateHelper) {
        $this->denyAccessUnlessGranted(SickNoteVoter::New);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        $students = [ ];

        /** @var User $user */
        $user = $this->getUser();

        if(EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent() ]) || $user->getStudents()->count() > 0) {
            $students = $user->getStudents()->toArray();

            if($user->getUserType()->equals(UserType::Student())) {
                $students = [ array_shift($students) ];
            }
        } else {
            $students = $studentRepository->findAll();
        }

        $note = new SickNote();
        $note->setFrom($timeHelper->getLessonDateForDateTime($this->getTodayOrNextDay($dateHelper, $settings->getNextDayThresholdTime())));
        $note->getUntil()->setLesson($timetableSettings->getMaxLessons());

        $form = $this->createForm(SickNoteType::class, $note, [
            'students' => $students
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $sender->sendSickNote($note, $user);

            $this->addFlash('success', 'sick_notes.add.success');
            return $this->redirectToRoute('sick_note');
        }

        return $this->render('sick_note/add.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
            'sick_notes' => $repository->findByStudents($user->getStudents()->toArray())
        ]);
    }

    /**
     * @Route("/sick_notes", name="sick_notes")
     */
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, Request $request,
                          SickNoteRepositoryInterface $sickNoteRepository, TuitionRepositoryInterface $tuitionRepository,
                          DateHelper $dateHelper, Sorter $sorter, Grouper $grouper) {
        $this->denyAccessUnlessGranted(SickNoteVoter::View);

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $request->query->get('teacher') !== '✗' && $gradeFilterView->getCurrentGrade() === null);
        $selectedDate = $dateHelper->getToday();

        try {
            if($request->query->has('date')) {
                if(empty($request->query->get('date'))) {
                    $selectedDate = null;
                } else {
                    $selectedDate = new DateTime($request->query->get('date', null));
                    $selectedDate->setTime(0, 0, 0);
                }
            }
        } catch (Exception $e) {
            $selectedDate = null;
        }

        $groups = [ ];

        if($teacherFilterView->getCurrentTeacher() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());

            foreach($tuitions as $tuition) {
                $students = $tuition->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudent();
                })->toArray();

                $sickNotes = $sickNoteRepository->findByStudents($students, $selectedDate);

                if(count($sickNotes) > 0) {
                    $group = new SickNoteTuitionGroup($tuition);

                    foreach($sickNotes as $note) {
                        $group->addItem($note);
                    }

                    $groups[] = $group;
                }
            }

            $sorter->sort($groups, SickNoteTuitionGroupStrategy::class);

        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $sickNotes = $sickNoteRepository->findByGrade($gradeFilterView->getCurrentGrade(), $selectedDate);

            if(count($sickNotes) > 0) {
                $group = new SickNoteGradeGroup($gradeFilterView->getCurrentGrade());

                foreach ($sickNotes as $note) {
                    $group->addItem($note);
                }

                $groups[] = $group;
            }

            $sorter->sort($groups, SickNoteGradeGroupStrategy::class);
        } else {
            $sickNotes = $sickNoteRepository->findAll($selectedDate);
            $groups = $grouper->group($sickNotes, SickNoteGradeStrategy::class, [ 'section' => $sectionFilterView->getCurrentSection() ]);
            $sorter->sort($groups, SickNoteGradeGroupStrategy::class);
        }

        $sorter->sortGroupItems($groups, SickNoteStrategy::class);

        return $this->render('sick_note/index.html.twig', [
            'today' => $dateHelper->getToday(),
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'selectedDate' => $selectedDate
        ]);
    }
}