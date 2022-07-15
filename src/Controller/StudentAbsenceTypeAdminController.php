<?php

namespace App\Controller;

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
 * @Route("/admin/absence_types")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class StudentAbsenceTypeAdminController extends AbstractController {
    private StudentAbsenceTypeRepositoryInterface $repository;

    public function __construct(StudentAbsenceTypeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_absence_types")
     */
    public function index() {
        return $this->render('admin/absence_types/index.html.twig', [
            'absence_types' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_absence_type")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{uuid}/edit", name="edit_absence_type")
     */
    public function edit(StudentAbsenceType $absenceType, Request $request) {
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

    /**
     * @Route("/{uuid}/remove", name="remove_absence_type")
     */
    public function remove(StudentAbsenceType $absenceType, Request $request) {
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