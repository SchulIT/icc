<?php

namespace App\Controller;

use App\Entity\DateLesson;
use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceAttachment;
use App\Entity\StudentAbsenceMessage;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Form\StudentAbsenceMessageType;
use App\Form\StudentAbsenceType;
use App\Grouping\StudentAbsenceGenericGroup;
use App\Grouping\StudentAbsenceGradeGroup;
use App\Grouping\StudentAbsenceStudentGroup;
use App\Grouping\StudentAbsenceTuitionGroup;
use App\Http\FlysystemFileResponse;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\StudentAbsenceVoter;
use App\Settings\StudentAbsenceSettings;
use App\Settings\TimetableSettings;
use App\Sorting\Sorter;
use App\Sorting\StudentAbsenceTuitionGroupStrategy;
use App\StudentAbsence\ApprovalHelper;
use App\Timetable\TimetableTimeHelper;
use App\Utils\EnumArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\GradeFilterView;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentAbsenceTypeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use League\Flysystem\FilesystemOperator;
use Mimey\MimeTypes;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/absences")
 * @Security("is_granted('ROLE_STUDENT_ABSENCE_CREATOR') or is_granted('ROLE_STUDENT_ABSENCE_VIEWER') or is_granted('new-student-absence')")
 */
class StudentAbsenceController extends AbstractController {

    const ITEMS_PER_PAGE = 25;

    private const CSRF_TOKEN_ID = 'student_absence_approval';

    use DateTimeHelperTrait;

    /**
     * @Route("/add", name="add_absence")
     */
    public function add(Request $request, StudentAbsenceSettings $settings,
                        StudentAbsenceRepositoryInterface $repository, StudentRepositoryInterface $studentRepository,
                        TimetableTimeHelper $timeHelper, TimetableSettings $timetableSettings, DateHelper $dateHelper) {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::New);

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

        $note = new StudentAbsence();
        $note->setFrom($timeHelper->getLessonDateForDateTime($this->getTodayOrNextDay($dateHelper, $settings->getNextDayThresholdTime())));
        $note->setUntil(new DateLesson());
        $note->getUntil()->setLesson($timetableSettings->getMaxLessons());

        $form = $this->createForm(StudentAbsenceType::class, $note, [
            'students' => $students
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($note);

            $this->addFlash('success', 'student_absences.add.success');
            return $this->redirectToRoute('absences');
        }

        return $this->render('absences/add.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
            'sick_notes' => $repository->findByStudents($user->getStudents()->toArray())
        ]);
    }

    /**
     * @Route("", name="absences")
     */
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, StudentFilter $studentFilter,
                          StudentAbsenceTypeFilter $typeFilter, Request $request,
                          StudentAbsenceRepositoryInterface $absenceRepository, TuitionRepositoryInterface $tuitionRepository,
                          SectionResolverInterface $sectionResolver, DateHelper $dateHelper, Sorter $sorter, StudentAbsenceSettings $settings) {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::CanViewAny);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);

        if($user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent())) {
            $gradeFilterView = new GradeFilterView([], null);
        }

        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $request->query->get('teacher') !== '✗' && $gradeFilterView->getCurrentGrade() === null && $studentFilterView->getCurrentStudent() === null);
        $typeFilterView = $typeFilter->handle($request->query->get('type'));

        $groups = [ ];

        $page = $request->query->getInt('page', 1);

        $paginator = null;

        if($teacherFilterView->getCurrentTeacher() !== null && $sectionFilterView->getCurrentSection() !== null) {
            $tuitions = $tuitionRepository->findAllByTeacher($teacherFilterView->getCurrentTeacher(), $sectionFilterView->getCurrentSection());

            foreach($tuitions as $tuition) {
                $students = $tuition->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudent();
                })->toArray();

                $absences = $absenceRepository->findByStudents($students, $typeFilterView->getCurrentType(), $dateHelper->getToday());

                if(count($absences) > 0) {
                    $group = new StudentAbsenceTuitionGroup($tuition);

                    foreach($absences as $note) {
                        if($this->isGranted(StudentAbsenceVoter::View, $note)) {
                            $group->addItem($note);
                        }
                    }

                    $groups[] = $group;
                }
            }

            $sorter->sort($groups, StudentAbsenceTuitionGroupStrategy::class);

        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $paginator = $absenceRepository->getGradePaginator($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection(), $typeFilterView->getCurrentType(), self::ITEMS_PER_PAGE, $page);

            if($paginator->count() > 0) {
                $group = new StudentAbsenceGradeGroup($gradeFilterView->getCurrentGrade());

                /** @var StudentAbsence $note */
                foreach ($paginator as $note) {
                    if($this->isGranted(StudentAbsenceVoter::View, $note)) {
                        $group->addItem($note);
                    }
                }

                $groups[] = $group;
            }
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $paginator = $absenceRepository->getStudentPaginator($studentFilterView->getCurrentStudent(), $typeFilterView->getCurrentType(), self::ITEMS_PER_PAGE, $page);

            if($paginator->count() > 0) {
                $group = new StudentAbsenceStudentGroup($studentFilterView->getCurrentStudent());

                /** @var StudentAbsence $note */
                foreach ($paginator as $note) {
                    if($this->isGranted(StudentAbsenceVoter::View, $note)) {
                        $group->addItem($note);
                    }
                }

                $groups[] = $group;
            }
        } else {
            $paginator = $absenceRepository->getPaginator($typeFilterView->getCurrentType(), self::ITEMS_PER_PAGE, $page);

            if($paginator->count() > 0) {
                $group = new StudentAbsenceGenericGroup();

                /** @var StudentAbsence $note */
                foreach ($paginator as $note) {
                    if($this->isGranted(StudentAbsenceVoter::View, $note)) {
                        $group->addItem($note);
                    }
                }

                $groups[] = $group;
            }
        }

        $pages = 0;
        if($paginator !== null) {
            $pages = ceil((double)$paginator->count() / self::ITEMS_PER_PAGE);
        }

        return $this->render('absences/index.html.twig', [
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'studentFilter' => $studentFilterView,
            'typeFilter' => $typeFilterView,
            'today' => $dateHelper->getToday(),
            'section' => $sectionResolver->getCurrentSection(),
            'pages' => $pages,
            'page' => $page,
            'isTeacherX' => $request->query->get('teacher') === '✗'
        ]);
    }

    /**
     * @Route("/{uuid}", name="show_absence")
     */
    public function show(StudentAbsence $absence, StudentAbsenceSettings $settings, Request $request, StudentAbsenceRepositoryInterface $repository) {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::View, $absence);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        $message = new StudentAbsenceMessage();
        $message->setAbsence($absence);
        $form = $this->createForm(StudentAbsenceMessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $absence->addMessage($message);
            $repository->persist($absence);

            $this->addFlash('success', 'student_absences.comment.success');
            return $this->redirectToRoute('show_absence', [
                'uuid' => $absence->getUuid()
            ]);
        }

        return $this->render('absences/show.html.twig', [
            'absence' => $absence,
            'token_id' => static::CSRF_TOKEN_ID,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/approve", name="approve_absence")
     */
    public function approve(StudentAbsence $absence, Request $request, ApprovalHelper $approvalHelper) {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Approve, $absence);

        if(!$this->isCsrfTokenValid(static::CSRF_TOKEN_ID, $request->query->get('_csrf_token'))) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else {
            $approvalHelper->setApprovalStatus($absence, true, $this->getUser());
            $this->addFlash('success', 'student_absences.approval.success');
        }

        return $this->redirectToRoute('show_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    /**
     * @Route("/{uuid}/deny", name="deny_absence")
     */
    public function deny(StudentAbsence $absence, Request $request, ApprovalHelper $approvalHelper) {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Deny, $absence);

        if(!$this->isCsrfTokenValid(static::CSRF_TOKEN_ID, $request->query->get('_csrf_token'))) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else {
            $approvalHelper->setApprovalStatus($absence, false, $this->getUser());
            $this->addFlash('success', 'student_absences.approval.success');
        }

        return $this->redirectToRoute('show_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    /**
     * @Route("/attachments/{uuid}", name="download_student_absence_attachment", priority="10")
     */
    public function downloadAttachment(StudentAbsenceAttachment $attachment, FilesystemOperator $studentAbsencesFilesystem, MimeTypes $mimeTypes, StudentAbsenceSettings $settings) {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::View, $attachment->getAbsence());

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        if($studentAbsencesFilesystem->fileExists($attachment->getPath()) !== true) {
            throw new NotFoundHttpException();
        }

        $extension = pathinfo($attachment->getFilename(), PATHINFO_EXTENSION);

        return new FlysystemFileResponse(
            $studentAbsencesFilesystem,
            $attachment->getPath(),
            $attachment->getFilename(),
            $mimeTypes->getMimeType($extension)
        );
    }
}