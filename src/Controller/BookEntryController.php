<?php

namespace App\Controller;

use App\Entity\LessonAttendance;
use App\Entity\LessonAttendanceType;
use App\Entity\LessonEntry;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Form\LessonEntryCancelType;
use App\Form\LessonEntryCreateType;
use App\Form\LessonEntryType;
use App\Repository\LessonEntryRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/cancel", name="cancel_lesson")
     */
    public function cancelLesson(Request $request, TuitionRepositoryInterface $tuitionRepository) {
        if($request->query->has('tuition') === null && $request->request->has('tuition') === null) {
            return $this->redirectToRequestReferer('book');
        }

        $tuition = $tuitionRepository->findOneByUuid($request->query->get('tuition', $request->request->get('tuition')));

        if($tuition === null) {
            throw new NotFoundHttpException();
        }

        $date = $this->getDateFromRequest($request, 'date');
        $lessonStart = $request->query->getInt('lesson_start');
        $lessonEnd = $request->query->getInt('lesson_end');

        $entry = (new LessonEntry())
            ->setTuition($tuition)
            ->setDate($date)
            ->setLessonStart($lessonStart)
            ->setLessonEnd($lessonEnd)
            ->setIsCancelled(true)
            ->setTeacher($tuition->getTeacher())
            ->setSubject($tuition->getSubject());

        $form = $this->createForm(LessonEntryCancelType::class, $entry);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // TODO: Validate if entry is related to a timetable lesson!?
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
     * @Route("/create", name="add_entry")
     */
    public function create(Request $request, TuitionRepositoryInterface $tuitionRepository) {
        if($request->query->has('tuition') === null && $request->request->has('tuition') === null) {
            return $this->redirectToRequestReferer('book');
        }

        $tuition = $tuitionRepository->findOneByUuid($request->query->get('tuition', $request->request->get('tuition')));

        if($tuition === null) {
            throw new NotFoundHttpException();
        }

        $date = $this->getDateFromRequest($request, 'date');

        $entry = (new LessonEntry())
            ->setTuition($tuition)
            ->setDate($date)
            ->setTeacher($tuition->getTeacher())
            ->setSubject($tuition->getSubject());

        $form = $this->createForm(LessonEntryCreateType::class, $entry);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $students = $tuition->getStudyGroup()->getMemberships()->map(function(StudyGroupMembership $membership) {
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
                    }
                }
            }

            // TODO: Validate if entry is related to a timetable lesson!?
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
     * @Route("/{uuid}", name="show_entry")
     */
    public function show(LessonEntry $entry, Request $request) {
        $form = $this->createForm(LessonEntryType::class, $entry, []);
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


}