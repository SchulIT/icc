<?php

namespace App\Controller;

use App\Entity\DateLesson;
use App\Entity\SickNote;
use App\Entity\SickNoteAttachment;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Form\SickNoteType;
use App\Grouping\Grouper;
use App\Grouping\SickNoteGradeGroup;
use App\Grouping\SickNoteGradeStrategy;
use App\Grouping\SickNoteStudentGroup;
use App\Grouping\SickNoteTuitionGroup;
use App\Http\FlysystemFileResponse;
use App\Repository\SickNoteRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\SickNoteVoter;
use App\Settings\SickNoteSettings;
use App\Settings\TimetableSettings;
use App\Sorting\SickNoteGradeGroupStrategy;
use App\Sorting\SickNoteStrategy;
use App\Sorting\SickNoteTuitionGroupStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Timetable\TimetableTimeHelper;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\SickNoteReasonFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use DateTime;
use Exception;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sick_notes")
 * @Security("is_granted('ROLE_SICK_NOTE_CREATOR') or is_granted('ROLE_SICK_NOTE_VIEWER') or is_granted('new-sicknote')")
 */
class SickNoteController extends AbstractController {

    const ITEMS_PER_PAGE = 25;

    use DateTimeHelperTrait;

    /**
     * @Route("/add", name="add_sick_note")
     */
    public function add(Request $request,/* SickNoteSender $sender, */SickNoteSettings $settings,
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
        $note->setUntil(new DateLesson());
        $note->getUntil()->setLesson($timetableSettings->getMaxLessons());

        $form = $this->createForm(SickNoteType::class, $note, [
            'students' => $students
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($note);

            $this->addFlash('success', 'sick_notes.add.success');
            return $this->redirectToRoute('sick_notes');
        }

        return $this->render('sick_note/add.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
            'sick_notes' => $repository->findByStudents($user->getStudents()->toArray())
        ]);
    }

    /**
     * @Route("", name="sick_notes")
     */
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, StudentFilter $studentFilter,
                          SickNoteReasonFilter $reasonFilter, Request $request,
                          SickNoteRepositoryInterface $sickNoteRepository, TuitionRepositoryInterface $tuitionRepository,
                          SectionResolverInterface $sectionResolver, DateHelper $dateHelper, Sorter $sorter, Grouper $grouper) {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $request->query->get('teacher') !== '✗' && $gradeFilterView->getCurrentGrade() === null && $studentFilterView->getCurrentStudent() === null);
        $reasonFilterView = $reasonFilter->handle($request->query->get('reason'));

        $groups = [ ];

        $page = $request->query->getInt('page', 1);

        $paginator = null;

        if($teacherFilterView->getCurrentTeacher() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());

            foreach($tuitions as $tuition) {
                $students = $tuition->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudent();
                })->toArray();

                $sickNotes = $sickNoteRepository->findByStudents($students, $reasonFilterView->getCurrentReason(), $dateHelper->getToday());

                if(count($sickNotes) > 0) {
                    $group = new SickNoteTuitionGroup($tuition);

                    foreach($sickNotes as $note) {
                        if($this->isGranted(SickNoteVoter::View, $note)) {
                            $group->addItem($note);
                        }
                    }

                    $groups[] = $group;
                }
            }

            $sorter->sort($groups, SickNoteTuitionGroupStrategy::class);

        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $paginator = $sickNoteRepository->getGradePaginator($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection(), $reasonFilterView->getCurrentReason(), static::ITEMS_PER_PAGE, $page);

            if($paginator->count() > 0) {
                $group = new SickNoteGradeGroup($gradeFilterView->getCurrentGrade());

                /** @var SickNote $note */
                foreach ($paginator as $note) {
                    if($this->isGranted(SickNoteVoter::View, $note)) {
                        $group->addItem($note);
                    }
                }

                $groups[] = $group;
            }
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $paginator = $sickNoteRepository->getStudentPaginator($studentFilterView->getCurrentStudent(), $reasonFilterView->getCurrentReason(), static::ITEMS_PER_PAGE, $page);

            if($paginator->count() > 0) {
                $group = new SickNoteStudentGroup($studentFilterView->getCurrentStudent());

                /** @var SickNote $note */
                foreach ($paginator as $note) {
                    if($this->isGranted(SickNoteVoter::View, $note)) {
                        $group->addItem($note);
                    }
                }

                $groups[] = $group;
            }
        }

        $pages = 0;
        if($paginator !== null) {
            $pages = ceil((double)$paginator->count() / static::ITEMS_PER_PAGE);
        }

        return $this->render('sick_note/index.html.twig', [
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'studentFilter' => $studentFilterView,
            'reasonFilter' => $reasonFilterView,
            'today' => $dateHelper->getToday(),
            'section' => $sectionResolver->getCurrentSection(),
            'pages' => $pages,
            'page' => $page
        ]);
    }

    /**
     * @Route("/{uuid}", name="show_sick_note")
     */
    public function show(SickNote $sickNote) {
        $this->denyAccessUnlessGranted(SickNoteVoter::View, $sickNote);

        return $this->render('sick_note/show.html.twig', [
            'note' => $sickNote
        ]);
    }

    /**
     * @Route("/attachments/{uuid}", name="download_sick_note_attachment", priority="10")
     */
    public function downloadAttachment(SickNoteAttachment $attachment, FilesystemInterface $sickNoteFilesystem, MimeTypes $mimeTypes) {
        $this->denyAccessUnlessGranted(SickNoteVoter::View, $attachment->getSickNote());

        if($sickNoteFilesystem->has($attachment->getPath()) !== true) {
            throw new NotFoundHttpException();
        }

        $extension = pathinfo($attachment->getFilename(), PATHINFO_EXTENSION);

        return new FlysystemFileResponse(
            $sickNoteFilesystem,
            $attachment->getPath(),
            $attachment->getFilename(),
            $mimeTypes->getMimeType($extension)
        );
    }
}