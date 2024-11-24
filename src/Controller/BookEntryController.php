<?php

namespace App\Controller;

use App\Entity\Attendance;
use App\Entity\AttendanceType;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\TimetableLesson;
use App\Entity\Tuition;
use App\Entity\User;
use App\Form\LessonAttendanceExcuseType;
use App\Form\LessonEntryAddStudent;
use App\Form\LessonEntryCancelType;
use App\Form\LessonEntryType;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use App\Security\Voter\LessonEntryVoter;
use DateMalformedStringException;
use DateTime;
use Doctrine\DBAL\Driver\Exception;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

        $tuition = $lesson->getTuition();

        $entry = (new LessonEntry())
            ->setLesson($lesson)
            ->setTuition($tuition)
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

    #[Route('/redirect/{uuid}', name: 'redirect_to_last_entry', methods: ['GET'])]
    public function redirectToLastEntry(Tuition $tuition, Request $request, LessonEntryRepositoryInterface $entryRepository): RedirectResponse {
        try {
            $dateTime = new DateTime($request->query->get('date'));
            $last = $entryRepository->findLastByTuition($tuition, $dateTime);

            if($last === null) {
                $this->addFlash('error', 'book.entry.redirect_to_last.error.not_found');
                return $this->redirectToRoute('book');
            }

            return $this->redirectToRoute('show_entry',  [
                'uuid' => $last->getUuid()->toString()
            ]);
        } catch(DateMalformedStringException) {
            $this->addFlash('error', 'book.entry.redirect_to_last.error.invalid_date');
            return $this->redirectToRoute('book');
        }
    }

    #[Route(path: '/{uuid}', name: 'show_entry', methods: ['GET'])]
    public function show(LessonEntry $entry, Request $request): Response {
        $this->denyAccessUnlessGranted(LessonEntryVoter::Edit, $entry);

        $form = $this->createForm(LessonEntryType::class, $entry, [
            'validation_groups' => [ $entry->isCancelled() ? 'cancel' : 'Default' ],
            'csrf_token_id' => 'book_entry'
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