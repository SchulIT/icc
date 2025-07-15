<?php

namespace App\Controller;

use App\Book\Grade\AdminOverview\OverviewHelper;
use App\Entity\Tuition;
use App\Entity\TuitionGradeCategory;
use App\Entity\TuitionGradeCatalog;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\AssignTuitionGradeCategoryType;
use App\Form\TuitionGradeCategoryType;
use App\Repository\TuitionGradeCategoryRepositoryInterface;
use App\Repository\TuitionGradeRepositoryInterface;
use App\Utils\ArrayUtils;
use App\View\Filter\GradesFilter;
use App\View\Filter\SectionFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/gradebook')]
#[IsFeatureEnabled(Feature::Book)]
#[IsFeatureEnabled(Feature::GradeBook)]
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

    #[Route('/overview', name: 'tuition_grades_overview')]
    public function overview(SectionFilter $sectionFilter, GradesFilter $gradesFilter, Request $request, #[CurrentUser] User $user, OverviewHelper $overviewHelper): Response {
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradesFilterView = $gradesFilter->handle($request->query->all('grades'), $sectionFilterView->getCurrentSection(), $user);

        $categories = ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(TuitionGradeCategory $category) => $category->getUuid()->toString()
        );

        $selectedCategories = [ ];

        foreach($request->query->all('categories') as $categoryUuid) {
            $category = $categories[$categoryUuid] ?? null;

            if($category !== null) {
                $selectedCategories[] = $category;
            }
        }

        $overview = null;

        if(count($gradesFilterView->getGrades()) > 0 && count($selectedCategories) > 0 && $sectionFilterView->getCurrentSection() !== null) {
            $overview = $overviewHelper->computeOverview($gradesFilterView->getCurrentGrades(), $selectedCategories, $sectionFilterView->getCurrentSection());
        }

        return $this->render('admin/tuition_grades/overview.html.twig', [
            'categories' => $categories,
            'sectionFilter' => $sectionFilterView,
            'gradesFilter' => $gradesFilterView,
            'selectedCategories' => $selectedCategories,
            'overview' => $overview
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
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] TuitionGradeCategory $category, Request $request): RedirectResponse|Response {
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
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] TuitionGradeCategory $category, Request $request, TuitionGradeRepositoryInterface $gradeRepository): RedirectResponse|Response {
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