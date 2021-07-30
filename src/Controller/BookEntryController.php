<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceType;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Form\LessonAttendanceExcuseType;
use App\Form\LessonEntryAddStudent;
use App\Form\LessonEntryCancelType;
use App\Form\LessonEntryCreateType;
use App\Form\LessonEntryType;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\LessonEntryRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book/entry")
 */
class BookEntryController extends AbstractController {

    use DateRequestTrait;

    private $repository;

    public function __construct(LessonEntryRepositoryInterface $entryRepository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $entryRepository;
    }

    /**
     * @Route("/cancel/{uuid}", name="cancel_lesson")
     */
    public function cancelLesson(Lesson $lesson, Request $request) {
        $lessonStart = $request->query->getInt('lesson_start');
        $lessonEnd = $request->query->getInt('lesson_end');
        $tuition = $lesson->getTuition();

        $entry = (new LessonEntry())
            ->setLesson($lesson)
            ->setTuition($tuition)
            ->setLessonStart($lessonStart)
            ->setLessonEnd($lessonEnd)
            ->setIsCancelled(true)
            ->setTeacher($tuition->getTeacher())
            ->setSubject($tuition->getSubject());

        $form = $this->createForm(LessonEntryCancelType::class, $entry);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($entry);
            $this->addFlash('success', 'book.entry.add.success');

            return $this->redirectToRoute('show_entry', [
                'uuid' => $entry->getUuid()->toString()
            ]);
        }

        return $this->render('books/cancel.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/create/{uuid}", name="add_entry")
     */
    public function create(Lesson $lesson, Request $request) {
        $entry = (new LessonEntry())
            ->setLesson($lesson)
            ->setTuition($lesson->getTuition())
            ->setTeacher($lesson->getTuition()->getTeacher())
            ->setSubject($lesson->getTuition()->getSubject());

        $form = $this->createForm(LessonEntryCreateType::class, $entry);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $students = $lesson->getTuition()->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
                return $membership->getStudent();
            });

            /** @var Student $student */
            foreach($students as $student) {
                $entry->addAttendance(
                    (new LessonAttendance())
                    ->setStudent($student)
                    ->setType(LessonAttendanceType::Present)
                    ->setEntry($entry)
                );
            }

            /** @var Student[] $absentStudent */
            $absentStudents = $form->get('absentStudents')->getData();

            /** @var LessonAttendance $attendance */
            foreach($entry->getAttendances() as $attendance) {
                foreach($absentStudents as $absentStudent) {
                    if($attendance->getStudent() == $absentStudent) {
                        $attendance->setType(LessonAttendanceType::Absent);
                        $attendance->setAbsentLessons($entry->getLessonEnd() - $entry->getLessonStart() + 1);
                    }
                }
            }

            $this->repository->persist($entry);
            $this->addFlash('success', 'book.entry.add.success');

            return $this->redirectToRoute('show_entry', [
                'uuid' => $entry->getUuid()->toString()
            ]);
        }

        return $this->render('books/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/attendance/{uuid}/excuse_status", name="change_lesson_attendance_excuse_status")
     */
    public function attendance(LessonAttendance $attendance, Request $request, LessonAttendanceRepositoryInterface $attendanceRepository) {
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

    /**
     * @Route("/{uuid}", name="show_entry")
     */
    public function show(LessonEntry $entry, Request $request) {
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

    /**
     * @Route("/{uuid}/students/add", name="add_student_to_entry")
     */
    public function addStudent(LessonEntry $entry, Request $request) {
        $form = $this->createForm(LessonEntryAddStudent::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var Student $student */
            $student = $form->get('student')->getData();

            $entry->addAttendance(
                (new LessonAttendance())
                    ->setStudent($student)
                    ->setType(LessonAttendanceType::Present)
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

    /**
     * @Route("/{uuid}/remove", name="remove_entry")
     */
    public function remove(LessonEntry $entry, Request $request) {
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