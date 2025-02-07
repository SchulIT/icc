<?php

namespace App\Controller;

use App\Entity\AppointmentCategory;
use App\Form\AppointmentCategoryType;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/appointments/categories')]
#[IsGranted('ROLE_APPOINTMENTS_ADMIN')]
class AppointmentCategoryAdminController extends AbstractController {

    public function __construct(private AppointmentCategoryRepositoryInterface $repository, private Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'admin_appointment_categories')]
    public function index(): Response {
        $categories = $this->repository->findAll();
        $this->sorter->sort($categories, AppointmentCategoryStrategy::class);

        return $this->render('admin/appointments/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/add', name: 'add_appointment_category')]
    public function add(Request $request): Response {
        $category = new AppointmentCategory();
        $form = $this->createForm(AppointmentCategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'admin.appointments.categories.add.success');
            return $this->redirectToRoute('admin_appointment_categories');
        }

        return $this->render('admin/appointments/categories/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_appointment_category')]
    public function edit(AppointmentCategory $category, Request $request): Response {
        $form = $this->createForm(AppointmentCategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'admin.appointments.categories.edit.success');
            return $this->redirectToRoute('admin_appointment_categories');
        }

        return $this->render('admin/appointments/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_appointment_category')]
    public function remove(AppointmentCategory $category, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.appointments.categories.remove.confirm', [
                '%name%' => $category->getName()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);

            $this->addFlash('success', 'admin.appointments.categories.remove.success');

            return $this->redirectToRoute('admin_appointment_categories');
        }

        return $this->render('admin/appointments/categories/remove.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}