<?php

namespace App\Controller;

use App\Entity\Tuition;
use App\Entity\TuitionGradeCategory;
use App\Entity\TuitionGradeCatalog;
use App\Form\AssignTuitionGradeCategoryType;
use App\Form\TuitionGradeCategoryType;
use App\Repository\TuitionGradeCategoryRepositoryInterface;
use App\Repository\TuitionGradeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/gradebook')]
class TuitionGradeCategoryAdminController extends AbstractController {

    public function __construct(private readonly TuitionGradeCategoryRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_tuition_grades')]
    public function index(): Response {
        return $this->render('admin/tuition_grades/index.html.twig', [
            'categories' => $this->repository->findAll()
        ]);
    }

    #[Route('/assign', name: 'assign_tuition_grades')]
    public function assign(Request $request): RedirectResponse|Response {
        $form = $this->createForm(AssignTuitionGradeCategoryType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var TuitionGradeCategory[] $categories */
            $categories = $form->get('categories')->getData();
            /** @var Tuition[] $tuitions */
            $tuitions = $form->get('tuitions')->getData();

            foreach($categories as $category) {
                foreach($tuitions as $tuition) {
                    if(!$category->getTuitions()->contains($tuition)) {
                        $category->getTuitions()->add($tuition);
                    }
                }

                $this->repository->persist($category);
            }

            $this->addFlash('success', 'admin.tuition_grades.assign.success');
            return $this->redirectToRoute('admin_tuition_grades');
        }

        return $this->render('admin/tuition_grades/assign.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/add', name: 'add_tuition_grade')]
    public function add(Request $request): RedirectResponse|Response {
        $category = new TuitionGradeCategory();
        $form = $this->createForm(TuitionGradeCategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);
            $this->addFlash('success', 'admin.grade_tuitions.add.success');

            return $this->redirectToRoute('admin_tuition_grades');
        }

        return $this->render('admin/tuition_grades/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_tuition_grade')]
    public function edit(TuitionGradeCategory $category, Request $request): RedirectResponse|Response {
        $form = $this->createForm(TuitionGradeCategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);
            $this->addFlash('success', 'admin.grade_tuitions.edit.success');

            return $this->redirectToRoute('admin_tuition_grades');
        }

        return $this->render('admin/tuition_grades/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_tuition_grade')]
    public function remove(TuitionGradeCategory $category, Request $request, TuitionGradeRepositoryInterface $gradeRepository): RedirectResponse|Response {
        $grades = $gradeRepository->countByTuitionGradeCategory($category);

        if($grades > 0) {
            $this->addFlash('error', 'admin.tuition_grades.remove.error');
            return $this->redirectToRoute('admin_tuition_grades');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.tuition_grades.remove.confirm',
            'message_parameters' => [
                '%name%' => $category->getDisplayName()
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);
            $this->addFlash('success', 'admin.tuition_grades.remove.success');

            return $this->redirectToRoute('admin_tuition_grades');
        }

        return $this->render('admin/tuition_grades/remove.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

}