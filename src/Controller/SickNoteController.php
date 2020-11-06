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
use App\Security\Voter\SickNoteVoter;
use App\Settings\SickNoteSettings;
use App\SickNote\SickNote;
use App\SickNote\SickNoteSender;
use App\Sorting\SickNoteStrategy;
use App\Sorting\SickNoteTuitionGroupStrategy;
use App\Sorting\Sorter;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\TeacherFilter;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SickNoteController extends AbstractController {

    /**
     * @Route("/sick_note", name="sick_note")
     */
    public function add(Request $request, SickNoteSender $sender, SickNoteSettings $settings,
                        SickNoteRepositoryInterface $repository, StudentRepositoryInterface $studentRepository) {
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
            'sick_notes' => $repository->findByUser($user)
        ]);
    }

    /**
     * @Route("/sick_notes", name="sick_notes")
     */
    public function index(GradeFilter $gradeFilter, TeacherFilter $teacherFilter, Request $request,
                          SickNoteRepositoryInterface $sickNoteRepository, TuitionRepositoryInterface $tuitionRepository,
                          DateHelper $dateHelper, Sorter $sorter, Grouper $grouper) {
        $this->denyAccessUnlessGranted(SickNoteVoter::View);

        /** @var User $user */
        $user = $this->getUser();

        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, $gradeFilterView->getCurrentGrade() === null);

        $groups = [ ];

        if($teacherFilterView->getCurrentTeacher() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher());

            foreach($tuitions as $tuition) {
                $students = $tuition->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudent();
                })->toArray();

                $sickNotes = $sickNoteRepository->findByStudents($students);

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
            $sickNotes = $sickNoteRepository->findByGrade($gradeFilterView->getCurrentGrade());

            if(count($sickNotes) > 0) {
                $group = new SickNoteGradeGroup($gradeFilterView->getCurrentGrade());

                foreach ($sickNotes as $note) {
                    $group->addItem($note);
                }

                $groups[] = $group;
            }
        } else {
            $sickNotes = $sickNoteRepository->findAll();
            $groups = $grouper->group($sickNotes, SickNoteGradeStrategy::class);
        }

        $sorter->sortGroupItems($groups, SickNoteStrategy::class);

        return $this->render('sick_note/index.html.twig', [
            'today' => $dateHelper->getToday(),
            'groups' => $groups,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView
        ]);
    }
}