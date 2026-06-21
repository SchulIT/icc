<?php

namespace App\StudentAbsence\Controller;

use App\Common\Entity\GradeTeacher;
use App\Common\View\Filter\DateRangeFilter;
use App\Framework\Controller\AbstractController;
use App\Framework\Controller\DateTimeHelperTrait;
use App\Common\Converter\StudyGroupStringConverter;
use App\Book\Entity\AttendanceExcuseStatus;
use App\Common\Entity\DateLesson;
use App\Exam\Entity\Exam;
use App\Framework\Http\Attribute\MapDateFromQuery;
use App\Notification\Repository\NotificationRepositoryInterface;
use App\StudentAbsence\Entity\StudentAbsence;
use App\StudentAbsence\Entity\StudentAbsenceAttachment;
use App\StudentAbsence\Entity\StudentAbsenceMessage;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\User;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Form\Model\BulkStudentAbsence;
use App\Form\Model\UpdateStudentBulkAbsence;
use App\StudentAbsence\Form\StudentAbsenceBulkType;
use App\StudentAbsence\Form\StudentAbsenceMessageType;
use App\StudentAbsence\Form\StudentAbsenceType;
use App\StudentAbsence\Form\UpdateStudentBulkAbsenceType;
use App\StudentAbsence\Grouping\StudentAbsenceGenericGroup;
use App\StudentAbsence\Grouping\StudentAbsenceGradeGroup;
use App\StudentAbsence\Grouping\StudentAbsenceStudentGroup;
use App\StudentAbsence\Grouping\StudentAbsenceTuitionGroup;
use App\Framework\Http\FlysystemFileResponse;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Exam\Repository\ExamRepositoryInterface;
use App\StudentAbsence\Repository\StudentAbsenceRepositoryInterface;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Repository\StudyGroupRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Book\Voter\ExcuseNoteVoter;
use App\StudentAbsence\Voter\StudentAbsenceVoter;
use App\StudentAbsence\Settings\StudentAbsenceSettings;
use App\Timetable\Settings\TimetableSettings;
use App\Appointment\Sorting\AppointmentStrategy;
use App\Exam\Sorting\ExamDateLessonStrategy;
use App\Framework\Sorting\Sorter;
use App\StudentAbsence\Sorting\StudentAbsenceTuitionGroupStrategy;
use App\Common\Sorting\StudyGroupStrategy;
use App\StudentAbsence\ApprovalHelper;
use App\StudentAbsence\AssociatedExcuseNoteManager;
use App\StudentAbsence\ExcuseStatusResolver;
use App\Timetable\TimetableTimeHelper;
use App\Common\View\Filter\GradeFilter;
use App\Common\View\Filter\GradeFilterView;
use App\Common\View\Filter\SectionFilter;
use App\StudentAbsence\View\Filter\StudentAbsenceTypeFilter;
use App\Common\View\Filter\StudentFilter;
use App\Common\View\Filter\TeacherFilter;
use DateTime;
use League\Flysystem\FilesystemOperator;
use Mimey\MimeTypes;
use Ramsey\Uuid\Uuid;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/absence/students')]
#[IsFeatureEnabled(Feature::StudentAbsence)]
#[IsGranted(new Expression("is_granted('ROLE_STUDENT_ABSENCE_CREATOR') or is_granted('ROLE_STUDENT_ABSENCE_VIEWER') or is_granted('new-absence')"))]
class StudentAbsenceController extends AbstractController {

    public const int ITEMS_PER_PAGE = 25;

    private const string CSRF_TOKEN_ID = 'student_absence_approval';

    use DateTimeHelperTrait;

    #[Route(path: '/add', name: 'add_student_absence')]
    public function add(Request $request, StudentAbsenceSettings $settings,
                        #[CurrentUser] User $user,
                        StudentAbsenceRepositoryInterface $repository,
                        TimetableTimeHelper $timeHelper, TimetableSettings $timetableSettings, DateHelper $dateHelper): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::New);

        $students = [ ];

        $note = new StudentAbsence();
        if($user->getStudents()->count() === 1) {
            $note->setStudent($user->getStudents()->first());
        }

        $note->setFrom($timeHelper->getLessonDateForDateTime($this->getTodayOrNextDay($dateHelper, $settings->getNextDayThresholdTime())));
        $note->setUntil((new DateLesson())->setDate(clone $note->getFrom()->getDate())->setLesson($timetableSettings->getMaxLessons()));

        $form = $this->createForm(StudentAbsenceType::class, $note);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($note);

            $this->addFlash('success', 'absences.students.add.success');
            return $this->redirectToRoute('show_student_absence', [
                'uuid' => $note->getUuid()
            ]);
        }

        return $this->render('absences/students/add.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
            'maxNumberOfAttachments' => StudentAbsence::MaxNumberOfAttachments
        ]);
    }

    #[Route(path: '/bulk/{uuid}', name: 'show_bulk_student_absence')]
    public function showBulk(string $uuid, Request $request, StudentAbsenceRepositoryInterface $absenceRepository, SectionResolverInterface $sectionResolver, ValidatorInterface $validator): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Bulk);

        $absences = $absenceRepository->findByBulkUuid($uuid);

        $toUpdate = new UpdateStudentBulkAbsence();
        $form = $this->createForm(UpdateStudentBulkAbsenceType::class, $toUpdate);
        $form->handleRequest($request);

        $numViolations = 0;

        if($form->isSubmitted() && $form->isValid()) {
            foreach($absences as $absence) {
                if (!empty($toUpdate->type)) {
                    $absence->setType($toUpdate->type);
                }

                if ($toUpdate->from !== null && $toUpdate->from->getDate() !== null) {
                    $absence->setFrom($toUpdate->from);
                }

                if ($toUpdate->until !== null && $toUpdate->until->getDate() !== null) {
                    $absence->setUntil($toUpdate->until);
                }

                if (!empty($toUpdate->message)) {
                    $absence->setMessage($toUpdate->message);
                }

                if (!empty($toUpdate->phone)) {
                    $absence->setPhone($toUpdate->phone);
                }

                if (!empty($toUpdate->email)) {
                    $absence->setEmail($toUpdate->email);
                }

                $violations = $validator->validate($absence);
                $numViolations += count($violations);

                if (count($violations) == 0) {
                    $absenceRepository->persist($absence);
                } else {
                    foreach ($violations as $violation) {
                        $this->addFlash('error', $violation->getMessage());
                    }
                }
            }

            if($numViolations === 0) {
                $this->addFlash('success', 'absences.students.bulk.success');
            }

            return $this->redirectToRoute('show_bulk_student_absence', [
                'uuid' => $uuid
            ]);
        }

        return $this->render('absences/students/bulk.html.twig', [
            'absences' => $absences,
            'section' => $sectionResolver->getCurrentSection(),
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/add_bulk', name: 'add_student_absence_bulk')]
    public function addBulk(Request $request, StudentAbsenceSettings $settings, SectionResolverInterface $sectionResolver,
                            StudentAbsenceRepositoryInterface $repository, StudyGroupRepositoryInterface $studyGroupRepository,
                            TimetableTimeHelper $timeHelper, TimetableSettings $timetableSettings, DateHelper $dateHelper,
                            Sorter $sorter, StudyGroupStringConverter $studyGroupStringConverter): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Bulk);

        $students = [ ];

        $note = new BulkStudentAbsence();
        $note->setFrom($timeHelper->getLessonDateForDateTime($this->getTodayOrNextDay($dateHelper, $settings->getNextDayThresholdTime())));
        $note->setUntil(new DateLesson());
        $note->getUntil()->setLesson($timetableSettings->getMaxLessons());

        $form = $this->createForm(StudentAbsenceBulkType::class, $note);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $uuid = Uuid::uuid4();

            // Create absence for each student
            foreach($note->getStudents() as $student) {
                $studentAbsence = (new StudentAbsence())
                    ->setBulkUuid($uuid)
                    ->setStudent($student)
                    ->setFrom($note->getFrom())
                    ->setUntil($note->getUntil())
                    ->setType($note->getType())
                    ->setMessage($note->getMessage())
                    ->setEmail($note->getEmail())
                    ->setPhone($note->getPhone());

                $repository->persist($studentAbsence);
            }

            $this->addFlash('success', 'absences.students.bulk.success');
            return $this->redirectToRoute('student_absences');
        }

        $studyGroupsData = [ ];

        $studyGroups = $studyGroupRepository->findAllBySection($sectionResolver->getCurrentSection());
        $sorter->sort($studyGroups, StudyGroupStrategy::class);

        foreach($studyGroups as $studyGroup) {
            $studyGroupsData[] = [
                'label' => $studyGroupStringConverter->convert($studyGroup, false, true),
                'students' => $studyGroup->getMemberships()->map(fn(StudyGroupMembership $m) => $m->getStudent()->getId())->toArray()
            ];
        }

        return $this->render('absences/students/add_bulk.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
            'studyGroupsData' => $studyGroupsData,
            'maxNumberOfAttachments' => StudentAbsence::MaxNumberOfAttachments
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_student_absence')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudentAbsence $absence, Request $request, StudentAbsenceSettings $settings, StudentAbsenceRepositoryInterface $repository): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::New);

        $form = $this->createForm(StudentAbsenceType::class, $absence);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($absence);

            $this->addFlash('success', 'absences.students.edit.success');
            return $this->redirectToRoute('show_student_absence', [
                'uuid' => $absence->getUuid()
            ]);
        }

        return $this->render('absences/students/edit.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
            'maxNumberOfAttachments' => StudentAbsence::MaxNumberOfAttachments
        ]);
    }

    #[Route(path: '', name: 'student_absences')]
    public function index(
            SectionFilter $sectionFilter,
            GradeFilter $gradeFilter,
            StudentFilter $studentFilter,
            StudentAbsenceTypeFilter $typeFilter,
            DateRangeFilter $rangeFilter,
            Request $request,
            StudentAbsenceRepositoryInterface $absenceRepository,
            StudentRepositoryInterface $studentRepository,
            UrlGeneratorInterface $urlGenerator,
            NotificationRepositoryInterface $notificationRepository,
            SectionResolverInterface $sectionResolver,
            DateHelper $dateHelper,
            ExcuseStatusResolver $excuseNoteStatusResolver,
            #[MapDateFromQuery] DateTime|null $start = null,
            #[MapDateFromQuery] DateTime|null $end = null
    ): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::CanViewAny);

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);

        if ($user->isStudentOrParent()) {
            $gradeFilterView = new GradeFilterView([], null, []);
        }

        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);
        $typeFilterView = $typeFilter->handle($request->query->get('type'));
        $rangeFilterView = $rangeFilter->handle($start, $end, $sectionFilterView->getCurrentSection());

        $page = $request->query->getInt('page', 1);

        $paginator = null;
        $absences = [];

        if ($gradeFilterView->getCurrentGrade() !== null) {
            $paginator = $absenceRepository->getGradePaginator($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection(), $typeFilterView->getCurrentType(), self::ITEMS_PER_PAGE, $page);
        } else {
            if ($studentFilterView->getCurrentStudent() !== null) {
                $paginator = $absenceRepository->getStudentPaginator($studentFilterView->getCurrentStudent(), $typeFilterView->getCurrentType(), self::ITEMS_PER_PAGE, $page, $rangeFilterView->start, $rangeFilterView->end);
            } else {
                if ($user->isTeacher()) {
                    $students = [];

                    /** @var GradeTeacher $gradeTeacher */
                    foreach ($user->getTeacher()->getGrades()->filter(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getSection()?->getId() === $sectionFilterView->getCurrentSection()?->getId()) as $gradeTeacher) {
                        $students = array_merge(
                            $students,
                            $studentRepository->findAllByGrade($gradeTeacher->getGrade(), $gradeTeacher->getSection())
                        );
                    }

                    $paginator = $absenceRepository->getStudentsPaginator($students, $typeFilterView->getCurrentType(), self::ITEMS_PER_PAGE, $page, $rangeFilterView->start, $rangeFilterView->end);
                } else {
                    $paginator = $absenceRepository->getPaginator($typeFilterView->getCurrentType(), self::ITEMS_PER_PAGE, $page, $rangeFilterView->start, $rangeFilterView->end);
                }
            }
        }

        $pages = 0;
        if ($paginator !== null) {
            /** @var StudentAbsence $note */
            foreach ($paginator as $note) {
                if ($this->isGranted(StudentAbsenceVoter::View, $note)) {
                    $absences[] = $note;
                }
            }

            $pages = ceil((double)$paginator->count() / self::ITEMS_PER_PAGE);
        }

        $excuseStatus = [];
        $unreadCounter = [];
        /** @var StudentAbsence $absence */
        foreach ($absences as $absence) {
            $excuseStatus[$absence->getUuid()->toString()] = $excuseNoteStatusResolver->getStatus($absence);
            $unreadCounter[$absence->getUuid()->toString()] = $notificationRepository->countUnreadForUserAndLink(
                $user,
                $urlGenerator->generate('show_student_absence', [
                    'uuid' => $absence->getUuid()
                ], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }


        return $this->render('absences/students/index.html.twig', [
            'absences' => $absences,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'studentFilter' => $studentFilterView,
            'typeFilter' => $typeFilterView,
            'rangeFilter' => $rangeFilterView,
            'today' => $dateHelper->getToday(),
            'section' => $sectionResolver->getCurrentSection(),
            'pages' => $pages,
            'page' => $page,
            'isTeacherX' => $request->query->get('teacher') === '✗',
            'excuseStatus' => $excuseStatus,
            'unreadCounter' => $unreadCounter,
        ]);
    }

    #[Route(path: '/{uuid}', name: 'show_student_absence')]
    public function show(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] StudentAbsence $absence,
        StudentAbsenceSettings $settings,
        Request $request,
        StudentAbsenceRepositoryInterface $repository,
        ExamRepositoryInterface $examRepository,
        AppointmentRepositoryInterface $appointmentRepository,
        Sorter $sorter,
        ExcuseStatusResolver $excuseNoteStatusResolver,
        SectionResolverInterface $sectionResolver,
        AssociatedExcuseNoteManager $excuseNoteManager
    ): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::View, $absence);

        $message = new StudentAbsenceMessage();
        $message->setAbsence($absence);
        $form = $this->createForm(StudentAbsenceMessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $absence->addMessage($message);
            $repository->persist($absence);

            $this->addFlash('success', 'absences.students.comment.success');
            return $this->redirectToRoute('show_student_absence', [
                'uuid' => $absence->getUuid()
            ]);
        }

        $exams = array_filter(
            $examRepository->findAllByStudents([$absence->getStudent()]),
            function(Exam $exam) use ($absence) {
                for($lessonNumber = $exam->getLessonStart(); $lessonNumber <= $exam->getLessonEnd(); $lessonNumber++) {
                    if((new DateLesson())->setDate(clone $exam->getDate())->setLesson($lessonNumber)->isBetween($absence->getFrom(), $absence->getUntil())) {
                        return true;
                    }
                }

                return false;
            }
        );
        $sorter->sort($exams, ExamDateLessonStrategy::class);

        $appointments = $appointmentRepository->findAllForStudentsAndTime([$absence->getStudent()], $absence->getFrom()->getDate(), $absence->getUntil()->getDate());
        $sorter->sort($appointments, AppointmentStrategy::class);

        return $this->render('absences/students/show.html.twig', [
            'absence' => $absence,
            'token_id' => self::CSRF_TOKEN_ID,
            'form' => $form->createView(),
            'exams' => $exams,
            'appointments' => $appointments,
            'excuseStatus' => $excuseNoteStatusResolver->getStatus($absence),
            'associatedExcuseNotes' => $excuseNoteManager->getAssociatedExcuseNotes($absence),
            'section' => $sectionResolver->getSectionForDate($absence->getFrom()->getDate())
        ]);
    }

    #[Route(path: '/{uuid}/excuse_note', name: 'add_excuse_note_from_absence')]
    public function createExcuseNote(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] StudentAbsence $absence,
        Request $request,
        AssociatedExcuseNoteManager $excuseNoteManager
    ): RedirectResponse {
        $this->denyAccessUnlessGranted(ExcuseNoteVoter::New);

        /** @var User $user */
        $user = $this->getUser();

        if($this->isCsrfTokenValid('excuse_note', $request->request->get('_csrf_token')) !== true) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else if($absence->getType()->getBookExcuseStatus() === AttendanceExcuseStatus::Excused) {
            $this->addFlash('success', 'absences.students.show.create_excuse_note.not_necessary');
        } else if($user->getTeacher() === null) {
            $this->addFlash('error', 'absences.students.show.create_excuse_note.teacher_required');
        } else {
            $excuseNoteManager->createOrUpdateExcuseNote($absence, $user->getTeacher());
            $this->addFlash('success', 'book.excuse_note.add.success');
        }

        return $this->redirectToRoute('show_student_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    #[Route(path: '/{uuid}/approve', name: 'approve_student_absence')]
    public function approve(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudentAbsence $absence, Request $request, ApprovalHelper $approvalHelper): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Approve, $absence);

        /** @var User $user */
        $user = $this->getUser();

        if(!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $request->query->get('_csrf_token'))) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else {
            $approvalHelper->setApprovalStatus($absence, true, $user);
            $this->addFlash('success', 'absences.students.approval.success');
        }

        return $this->redirectToRoute('show_student_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    #[Route(path: '/{uuid}/deny', name: 'deny_student_absence')]
    public function deny(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudentAbsence $absence, Request $request, ApprovalHelper $approvalHelper): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Deny, $absence);

        /** @var User $user */
        $user = $this->getUser();

        if(!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $request->query->get('_csrf_token'))) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else {
            $approvalHelper->setApprovalStatus($absence, false, $user);
            $this->addFlash('success', 'absences.students.approval.success');
        }

        return $this->redirectToRoute('show_student_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    #[Route(path: '/attachments/{uuid}', name: 'download_student_absence_attachment', priority: 10)]
    public function downloadAttachment(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudentAbsenceAttachment $attachment, FilesystemOperator $studentAbsencesFilesystem, MimeTypes $mimeTypes, StudentAbsenceSettings $settings): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::View, $attachment->getAbsence());

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

    #[Route('/{uuid}/remove', name: 'remove_student_absence')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudentAbsence $absence, Request $request, StudentAbsenceRepositoryInterface $repository): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Remove, $absence);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'absences.students.remove.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->remove($absence);

            $this->addFlash('success', 'absences.students.remove.success');

            return $this->redirectToRoute('student_absences');
        }

        return $this->render('absences/students/remove.html.twig', [
            'form' => $form->createView(),
            'absence' => $absence
        ]);
    }
}
