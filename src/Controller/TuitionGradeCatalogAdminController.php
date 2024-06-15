<?php

namespace App\Controller;

use App\Entity\TuitionGradeCatalog;
use App\Entity\TuitionGradeCatalogGrade;
use App\Form\TuitionGradeCatalogType;
use App\Repository\TuitionGradeCategoryRepositoryInterface;
use App\Repository\TuitionGradeCatalogRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/gradebook/catalogs')]
class TuitionGradeCatalogAdminController extends AbstractController {

    public function __construct(private readonly TuitionGradeCatalogRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_tuition_grade_catalogs')]
    public function index(): Response {
        return $this->render('admin/tuition_grades/catalogs/index.html.twig', [
            'types' => $this->repository->findAll()
        ]);
    }

    #[Route('/add', name: 'add_tuition_grade_catalog')]
    public function add(Request $request): RedirectResponse|Response {
        $preset = $request->query->get('preset');
        $presets = [
            'onetosix_notrend' => [ 1, 2, 3, 4, 5, 6 ],
            'onetosix_trend' => ['1+', '1', '1-', '2+', '2', '2-', '3+', '3', '3-', '4+', '4', '4-', '5+', '5', '5-', '6' ],
            'zerotofiveteen' => [15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
        ];

        $catalog = new TuitionGradeCatalog();

        if($preset !== null && isset($presets[$preset])) {
            foreach($presets[$preset] as $grade) {
                $catalog->addGrade((new TuitionGradeCatalogGrade())->setValue($grade));
            }
        }

        $form = $this->createForm(TuitionGradeCatalogType::class, $catalog);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($catalog);
            $this->addFlash('success', 'admin.tuition_grade_catalogs.add.success');

            return $this->redirectToRoute('admin_tuition_grade_catalogs');
        }

        return $this->render('admin/tuition_grades/catalogs/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_tuition_grade_catalog')]
    public function edit(TuitionGradeCatalog $catalog, Request $request): RedirectResponse|Response {
        $form = $this->createForm(TuitionGradeCatalogType::class, $catalog);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($catalog);
            $this->addFlash('success', 'admin.tuition_grade_catalogs.edit.success');

            return $this->redirectToRoute('admin_tuition_grade_catalogs');
        }

        return $this->render('admin/tuition_grades/catalogs/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_tuition_grade_catalog')]
    public function remove(TuitionGradeCatalog $catalog, Request $request, TuitionGradeCategoryRepositoryInterface $tuitionGradeCategoryRepository): RedirectResponse|Response {
        $categories = $tuitionGradeCategoryRepository->findAllByGradeType($catalog);

        if(count($categories) > 0) {
            $this->addFlash('error', 'admin.tuition_grade_catalogs.remove.error');
            return $this->redirectToRoute('admin_tuition_grade_catalogs');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.tuition_grade_catalogs.remove.confirm',
            'message_parameters' => [
                '%name%' => $catalog->getDisplayName()
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($catalog);
            $this->addFlash('success', 'admin.tuition_grade_catalogs.remove.success');

            return $this->redirectToRoute('admin_tuition_grade_catalogs');
        }

        return $this->render('admin/tuition_grades/catalogs/remove.html.twig', [
            'form' => $form->createView(),
            'type' => $catalog
        ]);


    }
}