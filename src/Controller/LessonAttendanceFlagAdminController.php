<?php

namespace App\Controller;

use App\Entity\AttendanceFlag;
use App\Form\LessonAttendanceFlagType;
use App\Repository\LessonAttendanceFlagRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/attendance_flags')]
class LessonAttendanceFlagAdminController extends AbstractController {

    public function __construct(private readonly LessonAttendanceFlagRepositoryInterface $repository ,RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_attendance_flags')]
    public function index() {
        return $this->render('admin/attendance_flags/index.html.twig', [
            'flags' => $this->repository->findAll()
        ]);
    }

    #[Route('/add', name: 'add_attendance_flags')]
    public function add(Request $request): RedirectResponse|Response {
        $flag = new AttendanceFlag();
        $form = $this->createForm(LessonAttendanceFlagType::class, $flag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($flag);
            $this->addFlash('success', 'admin.attendance_flags.add.success');
            return $this->redirectToRoute('admin_attendance_flags');
        }

        return $this->render('admin/attendance_flags/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_attendance_flags')]
    public function edit(AttendanceFlag $flag, Request $request) {
        $form = $this->createForm(LessonAttendanceFlagType::class, $flag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($flag);
            $this->addFlash('success', 'admin.attendance_flags.edit.success');
            return $this->redirectToRoute('admin_attendance_flags');
        }

        return $this->render('admin/attendance_flags/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_attendance_flags')]
    public function remove(AttendanceFlag $flag, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.attendance_flags.remove.confirm',
            'message_parameters' => [
                '%flag%' => $flag->getDescription()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($flag);
            $this->addFlash('success', 'admin.attendance_flags.remove.success');
            return $this->redirectToRoute('admin_attendance_flags');
        }

        return $this->render('admin/attendance_flags/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}