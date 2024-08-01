<?php

namespace App\Controller;

use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\TimetableLesson;
use App\Entity\User;
use App\Form\LessonAttendanceExcuseType;
use App\Form\LessonEntryAddStudent;
use App\Form\LessonEntryCancelType;
use App\Form\LessonEntryType;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Security\Voter\LessonEntryVoter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/book/entry')]
class BookEntryController extends AbstractController {

    use DateRequestTrait;

    public function __construct(private LessonEntryRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    private function redirectToReferrerInRequest(Request $request, string $fallbackRoute, array $fallbackParameters = [ ]): Response {
        $request->headers->set('referrer', $request->query->get('_ref'));
        return $this->redirectToRequestReferer($fallbackRoute, $fallbackParameters);
    }

    #[Route(path: '/cancel/{uuid}', name: 'cancel_lesson')]
    public function cancelLesson(TimetableLesson $lesson, Request $request): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $lessonStart = $request->query->getInt('lesson_start');
        $lessonEnd = $request->query->getInt('lesson_end');
        $tuition = $lesson->getTuition();

        $entry = (new LessonEntry())
            ->setLesson($lesson)
            ->setTuition($tuition)
            ->setLessonStart($lessonStart)
            ->setLessonEnd($lessonEnd)
            ->setIsCancelled(true)
            ->setTeacher($tuition->getTeachers()->first())
            ->setSubject($tuition->getSubject());

        /** @var User $user */
        $user = $this->getUser();
        if($user->getTeacher() !== null) {
            $entry->setReplacementTeacher($user->getTeacher());
        }

        $form = $this->createForm(LessonEntryCancelType::class, $entry, [
            'csrf_token_id' => 'book_entry'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($entry);
            $this->addFlash('success', 'book.entry.add.success');

            return $this->redirectToReferrerInRequest($request, 'show_entry', [
                'uuid' => $entry->getUuid()->toString()
            ]);
        }

        return $this->render('books/cancel.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}', name: 'edit_entry', methods: ['POST'])]
    public function edit(LessonEntry $entry, Request $request): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Edit, $entry);

        $form = $this->createForm(LessonEntryType::class, $entry, [
            'csrf_token_id' => 'book_entry'
        ]);
        $form->handleRequest($request);

        $cancelledForm = $this->createForm(LessonEntryCancelType::class, $entry, [
            'csrf_token_id' => 'book_entry'
        ]);
        $cancelledForm->handleRequest($request);

        if(($form->isSubmitted() && $form->isValid()) || ($cancelledForm->isSubmitted() && $cancelledForm->isValid())) {
            $this->repository->persist($entry);
            $this->addFlash('success', 'book.entry.edit.success');

            return $this->redirectToReferrerInRequest($request, 'books');
        }

        return $this->render('books/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/create/{uuid}', name: 'add_entry')]
    public function create(TimetableLesson $lesson, Request $request): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::New);

        $entry = (new LessonEntry())
            ->setLesson($lesson)
            ->setTuition($lesson->getTuition())
            ->setTeacher($lesson->getTuition()->getTeachers()->first())
            ->setSubject($lesson->getTuition()->getSubject());

        $form = $this->createForm(LessonEntryType::class, $entry, [
            'csrf_token_id' => 'book_entry'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($entry);
            $this->addFlash('success', 'book.entry.add.success');

            return $this->redirectToReferrerInRequest($request, 'show_entry', [
                'uuid' => $entry->getUuid()->toString()
            ]);
        }

        return $this->render('books/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/attendance/{uuid}/excuse_status', name: 'change_lesson_attendance_excuse_status')]
    public function attendance(Attendance $attendance, Request $request, LessonAttendanceRepositoryInterface $attendanceRepository): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Edit, $attendance->getEntry());

        $form = $this->createForm(LessonAttendanceExcuseType::class, $attendance);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $attendanceRepository->persist($attendance);
            return $this->redirectToRequestReferer('book');
        }

        return $this->render('books/attendance.html.twig', [
            'attendance' => $attendance,
            'entry' => $attendance->getEntry(),
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}', name: 'show_entry')]
    public function show(LessonEntry $entry, Request $request): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Edit, $entry);

        $form = $this->createForm(LessonEntryType::class, $entry, [
            'validation_groups' => [ $entry->isCancelled() ? 'cancel' : 'Default' ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($entry);

            $this->addFlash('success', 'book.entry.edit.success');
            return $this->redirectToRoute('show_entry', [
                'uuid' => $entry->getUuid()->toString()
            ]);
        }

        return $this->render('books/entry/show.html.twig', [
            'entry' => $entry,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/students/add', name: 'add_student_to_entry')]
    public function addStudent(LessonEntry $entry, Request $request): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Edit, $entry);

        $form = $this->createForm(LessonEntryAddStudent::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var Student $student */
            $student = $form->get('student')->getData();

            $entry->addAttendance(
                (new Attendance())
                    ->setStudent($student)
                    ->setType(AttendanceType::Present)
                    ->setEntry($entry)
            );

            $this->repository->persist($entry);

            $this->addFlash('success', 'book.entry.student.add.success');

            return $this->redirectToRoute('show_entry', [
                'uuid' => $entry->getUuid()->toString()
            ]);
        }

        return $this->render('books/entry/add_student.html.twig', [
            'form' => $form->createView(),
            'entry' => $entry
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_entry')]
    public function remove(LessonEntry $entry, Request $request): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Remove, $entry);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'book.entry.remove.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($entry);
            $this->addFlash('success', 'book.entry.remove.success');

            return $this->redirectToRoute('book');
        }

        return $this->render('books/entry/remove.html.twig', [
            'form' => $form->createView(),
            'entry' => $entry
        ]);
    }
}