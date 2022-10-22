<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\StudentAbsenceType;
use App\Form\StudentAbsenceTypeType;
use App\Repository\StudentAbsenceTypeRepositoryInterface;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route(path: '/admin/absence_types')]
class StudentAbsenceTypeAdminController extends AbstractController {
    public function __construct(private StudentAbsenceTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'admin_absence_types')]
    public function index(): Response {
        return $this->render('admin/absence_types/index.html.twig', [
            'absence_types' => $this->repository->findAll()
        ]);
    }

    #[Route(path: '/add', name: 'add_absence_type')]
    public function add(Request $request): Response {
        $absenceType = new StudentAbsenceType();
        $form = $this->createForm(StudentAbsenceTypeType::class, $absenceType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($absenceType);
            $this->addFlash('success', 'admin.absence_types.add.success');
            return $this->redirectToRoute('admin_absence_types');
        }

        return $this->render('admin/absence_types/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_absence_type')]
    public function edit(StudentAbsenceType $absenceType, Request $request): Response {
        $form = $this->createForm(StudentAbsenceTypeType::class, $absenceType);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($absenceType);
            $this->addFlash('success', 'admin.absence_types.edit.success');
            return $this->redirectToRoute('admin_absence_types');
        }

        return $this->render('admin/absence_types/edit.html.twig', [
            'form' => $form->createView(),
            'absence_type' => $absenceType
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_absence_type')]
    public function remove(StudentAbsenceType $absenceType, Request $request): Response {
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

            return $this->redirectToRoute('admin_absence_types');
        }

        return $this->render('admin/absence_types/remove.html.twig', [
            'form' => $form->createView(),
            'absence_type' => $absenceType
        ]);
    }

}