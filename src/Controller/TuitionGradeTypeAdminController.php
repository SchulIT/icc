<?php

namespace App\Controller;

use App\Entity\TuitionGradeType;
use App\Form\TuitionGradeTypeType;
use App\Repository\TuitionGradeTypeRepositoryInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/gradebook/grades')]
class TuitionGradeTypeAdminController extends AbstractController {

    public function __construct(private readonly TuitionGradeTypeRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_tuition_grade_types')]
    public function index(): Response {
        return $this->render('admin/tuition_grades/types/index.html.twig', [
            'types' => $this->repository->findAll()
        ]);
    }

    #[Route('/add', name: 'add_tuition_grade_type')]
    public function add(Request $request): RedirectResponse|Response {
        $type = new TuitionGradeType();
        $form = $this->createForm(TuitionGradeTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);
            $this->addFlash('success', 'admin.tuition_grade_types.add.success');

            return $this->redirectToRoute('admin_tuition_grade_types');
        }

        return $this->render('admin/tuition_grades/types/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_tuition_grade_type')]
    public function edit(TuitionGradeType $type, Request $request): RedirectResponse|Response {
        $form = $this->createForm(TuitionGradeTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);
            $this->addFlash('success', 'admin.tuition_grade_types.edit.success');

            return $this->redirectToRoute('admin_tuition_grade_types');
        }

        return $this->render('admin/tuition_grades/types/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_tuition_grade_type')]
    public function remove(): RedirectResponse|Response {

    }
}