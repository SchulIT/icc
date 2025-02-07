<?php

namespace App\Controller;

use App\Entity\TeacherAbsenceType;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\TeacherAbsenceTypeType;
use App\Repository\TeacherAbsenceTypeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/absence_types/teachers')]
#[IsGranted('ROLE_ADMIN')]
#[IsFeatureEnabled(Feature::TeacherAbsence)]
class TeacherAbsenceTypeAdminController extends AbstractController {
    public function __construct(private readonly TeacherAbsenceTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'admin_teacher_absence_types')]
    public function index(): Response {
        return $this->render('admin/absence_types/teachers/index.html.twig', [
            'absence_types' => $this->repository->findAll()
        ]);
    }

    #[Route(path: '/add', name: 'add_teacher_absence_type')]
    public function add(Request $request): Response {
        $absenceType = new TeacherAbsenceType();
        $form = $this->createForm(TeacherAbsenceTypeType::class, $absenceType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($absenceType);
            $this->addFlash('success', 'admin.absence_types.add.success');
            return $this->redirectToRoute('admin_teacher_absence_types');
        }

        return $this->render('admin/absence_types/teachers/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_teacher_absence_type')]
    public function edit(TeacherAbsenceType $absenceType, Request $request): Response {
        $form = $this->createForm(TeacherAbsenceTypeType::class, $absenceType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($absenceType);
            $this->addFlash('success', 'admin.absence_types.edit.success');
            return $this->redirectToRoute('admin_absence_types');
        }

        return $this->render('admin/absence_types/teachers/edit.html.twig', [
            'form' => $form->createView(),
            'absence_type' => $absenceType
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_teacher_absence_type')]
    public function remove(TeacherAbsenceType $absenceType, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.absence_types.remove.confirm',
            'message_parameters' => [
                '%name%' => $absenceType->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($absenceType);
            $this->addFlash('success', 'admin.absence_types.remove.success');

            return $this->redirectToRoute('admin_teacher_absence_types');
        }

        return $this->render('admin/absence_types/teachers/remove.html.twig', [
            'form' => $form->createView(),
            'absence_type' => $absenceType
        ]);
    }

}