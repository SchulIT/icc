<?php

namespace App\Common\Controller;

use App\Framework\Controller\AbstractController;
use App\Substitution\Repository\AbsenceRepositoryInterface;
use App\Appointment\Repository\AppointmentRepositoryInterface;
use App\Book\Repository\BookCommentRepositoryInterface;
use App\Book\Repository\BookEventRepositoryInterface;
use App\Common\Repository\StudentInformationRepositoryInterface;
use App\Exam\Repository\ExamRepositoryInterface;
use App\Book\Repository\ExcuseNoteRepositoryInterface;
use App\Substitution\Repository\FreeTimespanRepositoryInterface;
use App\Substitution\Repository\InfotextRepositoryInterface;
use App\Notification\Repository\NotificationRepositoryInterface;
use App\ParentsDay\Repository\ParentsDayRepositoryInterface;
use App\Common\Repository\ResourceReservationRepositoryInterface;
use App\StudentAbsence\Repository\StudentAbsenceRepositoryInterface;
use App\Substitution\Repository\SubstitutionRepositoryInterface;
use App\TeacherAbsence\Repository\TeacherAbsenceRepositoryInterface;
use App\Timetable\Repository\TimetableSupervisionRepositoryInterface;
use App\Grade\Repository\TuitionGradeRepositoryInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Common\Entity\Section;
use App\Common\Form\SectionType;
use App\Common\Repository\SectionRepositoryInterface;
use App\Timetable\Repository\TimetableLessonRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/section')]
class SectionController extends AbstractController {

    public function __construct(private SectionRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_sections')]
    public function index(): Response {
        return $this->render('admin/sections/index.html.twig', [
            'sections' => $this->repository->findAll()
        ]);
    }

    #[Route(path: '/add', name: 'add_section')]
    public function add(Request $request): Response {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($section);
            $this->addFlash('success', 'admin.sections.add.success');

            return $this->redirectToRoute('admin_sections');
        }

        return $this->render('admin/sections/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_section')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Section $section, Request $request): Response {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($section);
            $this->addFlash('success', 'admin.sections.add.success');

            return $this->redirectToRoute('admin_sections');
        }

        return $this->render('admin/sections/edit.html.twig', [
            'section' => $section,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_section')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Section $section, Request $request, TimetableLessonRepositoryInterface $lessonRepository,
                           TuitionGradeRepositoryInterface                   $gradeRepository, StudentAbsenceRepositoryInterface $studentAbsenceRepository,
                           TeacherAbsenceRepositoryInterface                 $teacherAbsenceRepository, TimetableSupervisionRepositoryInterface $supervisionRepository,
                           ExamRepositoryInterface                           $examRepository, SubstitutionRepositoryInterface $substitutionRepository,
                           NotificationRepositoryInterface                   $notificationRepository, ResourceReservationRepositoryInterface $reservationRepository,
                           AbsenceRepositoryInterface                        $absenceRepository, FreeTimespanRepositoryInterface $freeTimespanRepository,
                           InfotextRepositoryInterface                       $infotextRepository, AppointmentRepositoryInterface $appointmentRepository,
                           ExcuseNoteRepositoryInterface                     $excuseNoteRepository, BookCommentRepositoryInterface $bookCommentRepository,
                           ParentsDayRepositoryInterface                     $parentsDayRepository, StudentInformationRepositoryInterface $studentInformationRepository,
                           BookEventRepositoryInterface                      $bookEventRepository): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.sections.remove.confirm',
            'message_parameters' => [
                '%name%' => $section->getDisplayName()
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lessonRepository->removeRange($section->getStart(), $section->getEnd());
            $supervisionRepository->removeBetween($section->getStart(), $section->getEnd());
            $examRepository->removeBetween($section->getStart(), $section->getEnd());
            $substitutionRepository->removeBetween($section->getStart(), $section->getEnd());
            $notificationRepository->removeBetween($section->getStart(), $section->getEnd());
            $reservationRepository->removeBetween($section->getStart(), $section->getEnd());
            $freeTimespanRepository->removeBetween($section->getStart(), $section->getEnd());
            $absenceRepository->removeBetween($section->getStart(), $section->getEnd());
            $infotextRepository->removeBetween($section->getStart(), $section->getEnd());
            $appointmentRepository->removeBetween($section->getStart(), $section->getEnd());
            $excuseNoteRepository->removeBetween($section->getStart(), $section->getEnd());
            $bookCommentRepository->removeRange($section->getStart(), $section->getEnd());
            $bookEventRepository->removeRange($section->getStart(), $section->getEnd());
            $gradeRepository->removeForSection($section);
            $studentAbsenceRepository->removeRange($section->getStart(), $section->getEnd());
            $teacherAbsenceRepository->removeRange($section->getStart(), $section->getEnd());
            $parentsDayRepository->removeRange($section->getStart(), $section->getEnd());
            $studentInformationRepository->removeExpired($section->getEnd());

            $this->repository->remove($section);
            $this->addFlash('success', 'admin.sections.remove.success');

            return $this->redirectToRoute('admin_sections');
        }

        return $this->render('admin/sections/remove.html.twig', [
            'section' => $section,
            'form' => $form->createView()
        ]);
    }
}