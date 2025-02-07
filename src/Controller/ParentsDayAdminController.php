<?php

namespace App\Controller;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayAppointment;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\ParentsDayType;
use App\Repository\ParentsDayRepositoryInterface;
use App\Security\Voter\AppointmentVoter;
use App\Security\Voter\ParentsDayAppointmentVoter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/parents_day')]
#[IsFeatureEnabled(Feature::ParentsDay)]
class ParentsDayAdminController extends AbstractController {
    public function __construct(RefererHelper $redirectHelper, private readonly ParentsDayRepositoryInterface $repository) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_parents_days')]
    public function index() {
        return $this->render('admin/parents_days/index.html.twig', [
            'parents_days' => $this->repository->findAll()
        ]);
    }

    #[Route('/add', name: 'add_parents_day')]
    public function add(Request $request) {
        $parentsDay = new ParentsDay();

        $form = $this->createForm(ParentsDayType::class, $parentsDay);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($parentsDay);
            $this->addFlash('success', 'admin.parents_day.add.success');

            return $this->redirectToRoute('admin_parents_days');
        }

        return $this->render('admin/parents_days/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_parents_day')]
    public function edit(ParentsDay $parentsDay, Request $request) {
        $form = $this->createForm(ParentsDayType::class, $parentsDay);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($parentsDay);
            $this->addFlash('success', 'admin.parents_day.edit.success');

            return $this->redirectToRoute('admin_parents_days');
        }

        return $this->render('admin/parents_days/edit.html.twig', [
            'parents_day' => $parentsDay,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_parents_day')]
    public function remove(ParentsDay $parentsDay, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.parents_day.remove.confirm',
            'message_parameters' => [
                '%title%' => $parentsDay->getTitle()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($parentsDay);

            $this->addFlash('success', 'admin.parents_day.remove.success');

            return $this->redirectToRoute('admin_appointments');
        }

        return $this->render('admin/parents_days/remove.html.twig', [
            'form' => $form->createView(),
            'parents_day' => $parentsDay
        ]);
    }
}