<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Form\AppointmentCategoryType;
use App\Repository\AppointmentCategoryRepositoryInterface;
use App\Sorting\AppointmentCategoryStrategy;
use App\Sorting\Sorter;
use SchoolIT\CommonBundle\Form\ConfirmType;
use SchoolIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/appointments/categories")
 * @Security("is_granted('ROLE_APPOINTMENTS_ADMIN')")
 */
class AppointmentCategoryAdminController extends AbstractController {

    private $repository;
    private $sorter;

    public function __construct(AppointmentCategoryRepositoryInterface $categoryRepository, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->repository = $categoryRepository;
        $this->sorter = $sorter;
    }

    /**
     * @Route("", name="admin_appointment_categories")
     */
    public function index() {
        $categories = $this->repository->findAll();
        $this->sorter->sort($categories, AppointmentCategoryStrategy::class);

        return $this->render('admin/appointments/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/add", name="add_appointment_category")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{uuid}/edit", name="edit_appointment_category")
     */
    public function edit(AppointmentCategory $category, Request $request) {
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

    /**
     * @Route("/{uuid}/remove", name="remove_appointment_category")
     */
    public function remove(AppointmentCategory $category, Request $request, TranslatorInterface $translator) {
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