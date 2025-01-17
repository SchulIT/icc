<?php

namespace App\Controller;

use App\Converter\StudyGroupStringConverter;
use App\Entity\DateLesson;
use App\Entity\Exam;
use App\Entity\ExcuseNote;
use App\Entity\AttendanceExcuseStatus;
use App\Entity\Student;
use App\Entity\StudentAbsence;
use App\Entity\StudentAbsenceAttachment;
use App\Entity\StudentAbsenceMessage;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Form\Model\BulkStudentAbsence;
use App\Form\StudentAbsenceBulkType;
use App\Form\StudentAbsenceMessageType;
use App\Form\StudentAbsenceType;
use App\Grouping\StudentAbsenceGenericGroup;
use App\Grouping\StudentAbsenceGradeGroup;
use App\Grouping\StudentAbsenceStudentGroup;
use App\Grouping\StudentAbsenceTuitionGroup;
use App\Http\FlysystemFileResponse;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\ExcuseNoteVoter;
use App\Security\Voter\StudentAbsenceVoter;
use App\Settings\StudentAbsenceSettings;
use App\Settings\TimetableSettings;
use App\Sorting\AppointmentStrategy;
use App\Sorting\ExamDateLessonStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentAbsenceTuitionGroupStrategy;
use App\Sorting\StudyGroupStrategy;
use App\StudentAbsence\ApprovalHelper;
use App\StudentAbsence\ExcuseStatusResolver;
use App\Timetable\TimetableTimeHelper;
use App\View\Filter\GradeFilter;
use App\View\Filter\GradeFilterView;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentAbsenceTypeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use League\Flysystem\FilesystemOperator;
use Mimey\MimeTypes;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/absence/students')]
#[Security("is_granted('ROLE_STUDENT_ABSENCE_CREATOR') or is_granted('ROLE_STUDENT_ABSENCE_VIEWER') or is_granted('new-absence')")]
class StudentAbsenceController extends AbstractController {

    public const ITEMS_PER_PAGE = 25;

    private const CSRF_TOKEN_ID = 'student_absence_approval';

    use DateTimeHelperTrait;

    #[Route(path: '/add', name: 'add_student_absence')]
    public function add(Request $request, StudentAbsenceSettings $settings,
                        StudentAbsenceRepositoryInterface $repository, StudentRepositoryInterface $studentRepository,
                        TimetableTimeHelper $timeHelper, TimetableSettings $timetableSettings, DateHelper $dateHelper): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::New);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        $students = [ ];

        $note = new StudentAbsence();
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
            'settings' => $settings
        ]);
    }

    #[Route(path: '/add_bulk', name: 'add_student_absence_bulk')]
    public function addBulk(Request $request, StudentAbsenceSettings $settings, SectionResolverInterface $sectionResolver,
                            StudentAbsenceRepositoryInterface $repository, StudyGroupRepositoryInterface $studyGroupRepository,
                            TimetableTimeHelper $timeHelper, TimetableSettings $timetableSettings, DateHelper $dateHelper,
                            Sorter $sorter, StudyGroupStringConverter $studyGroupStringConverter): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::Bulk);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        $students = [ ];

        $note = new BulkStudentAbsence();
        $note->setFrom($timeHelper->getLessonDateForDateTime($this->getTodayOrNextDay($dateHelper, $settings->getNextDayThresholdTime())));
        $note->setUntil(new DateLesson());
        $note->getUntil()->setLesson($timetableSettings->getMaxLessons());

        $form = $this->createForm(StudentAbsenceBulkType::class, $note);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Create absence for each student
            /** @var Student $student */
            foreach($note->getStudents() as $student) {
                $studentAbsence = (new StudentAbsence())
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
            'studyGroupsData' => $studyGroupsData
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_student_absence')]
    public function edit(StudentAbsence $absence, Request $request, StudentAbsenceSettings $settings, StudentAbsenceRepositoryInterface $repository): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::New);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

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
            'settings' => $settings
        ]);
    }

    #[Route(path: '', name: 'student_absences')]
    public function index(SectionFilter                     $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, StudentFilter $studentFilter,
                          StudentAbsenceTypeFilter          $typeFilter, Request $request,
                          StudentAbsenceRepositoryInterface $absenceRepository, TuitionRepositoryInterface $tuitionRepository,
                          SectionResolverInterface          $sectionResolver, DateHelper $dateHelper, Sorter $sorter, StudentAbsenceSettings $settings, ExcuseStatusResolver $excuseNoteStatusResolver): Response {
        $this->denyAccessUnlessGranted(StudentAbsenceVoter::CanViewAny);

        if($settings->isEnabled() !== true) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);

        if($user->isStudentOrParent()) {
            $gradeFilterView = new GradeFilterView([], null, []);
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
                $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

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

        $excuseStatus = [ ];
        foreach($groups as $group) {
            /** @var StudentAbsence $absence */
            foreach($group->getAbsences() as $absence) {
                $excuseStatus[$absence->getUuid()->toString()] = $excuseNoteStatusResolver->getStatus($absence);
            }
        }

        return $this->render('absences/students/index.html.twig', [
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
            'isTeacherX' => $request->query->get('teacher') === '✗',
            'excuseStatus' => $excuseStatus
        ]);
    }

    #[Route(path: '/{uuid}', name: 'show_student_absence')]
    public function show(StudentAbsence $absence, StudentAbsenceSettings $settings, Request $request, StudentAbsenceRepositoryInterface $repository, ExamRepositoryInterface $examRepository,
                         AppointmentRepositoryInterface $appointmentRepository, Sorter $sorter, ExcuseStatusResolver $excuseNoteStatusResolver, SectionResolverInterface $sectionResolver): Response {
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
            'section' => $sectionResolver->getSectionForDate($absence->getFrom()->getDate())
        ]);
    }

    #[Route(path: '/{uuid}/excuse_note', name: 'add_excuse_note_from_absence')]
    public function createExcuseNote(StudentAbsence $absence, Request $request, ExcuseNoteRepositoryInterface $excuseNoteRepository) {
        $this->denyAccessUnlessGranted(ExcuseNoteVoter::New);

        /** @var User $user */
        $user = $this->getUser();

        if($this->isCsrfTokenValid('excuse_note', $request->request->get('_csrf_token')) !== true) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else if($absence->getType()->getBookExcuseStatus() === AttendanceExcuseStatus::Excused) {
            $this->addFlash('success', 'absences.students.show.create_excuse_note.not_necessary');
        } else {
            $comment = 'student_absence:' . $absence->getUuid()->toString();
            $existingNotes = $excuseNoteRepository->findByStudentAndComment($absence->getStudent(), $comment);

            if(count($existingNotes) === 0) {
                $excuseNote = (new ExcuseNote())
                    ->setStudent($absence->getStudent())
                    ->setComment($comment);
            } else {
                $excuseNote = array_shift($existingNotes);
            }

            $excuseNote
                ->setFrom($absence->getFrom())
                ->setUntil($absence->getUntil())
                ->setExcusedBy($user->getTeacher());

            $excuseNoteRepository->persist($excuseNote);
            $this->addFlash('success', 'book.excuse_note.add.success');
        }

        return $this->redirectToRoute('show_student_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    #[Route(path: '/{uuid}/approve', name: 'approve_student_absence')]
    public function approve(StudentAbsence $absence, Request $request, ApprovalHelper $approvalHelper): Response {
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
    public function deny(StudentAbsence $absence, Request $request, ApprovalHelper $approvalHelper): Response {
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
    public function downloadAttachment(StudentAbsenceAttachment $attachment, FilesystemOperator $studentAbsencesFilesystem, MimeTypes $mimeTypes, StudentAbsenceSettings $settings): Response {
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

    #[Route('/{uuid}/remove', name: 'remove_student_absence')]
    public function remove(StudentAbsence $absence, Request $request, StudentAbsenceRepositoryInterface $repository): Response {
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