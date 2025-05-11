<?php

namespace App\Controller;

use App\Entity\ReturnItemType;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\ReturnItemTypeType;
use App\Repository\ReturnItemRepositoryInterface;
use App\Repository\ReturnItemTypeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/return_items/types')]
#[IsFeatureEnabled(Feature::ReturnItem)]
class ReturnItemTypeAdminController extends AbstractController {
    public function __construct(RefererHelper $redirectHelper, private readonly ReturnItemTypeRepositoryInterface $repository) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_return_item_types')]
    public function index(): Response {
        return $this->render('admin/return_item_types/index.html.twig', [
            'types' => $this->repository->findAll()
        ]);
    }

    #[Route('/add', name: 'add_return_item_type')]
    public function add(Request $request): Response {
        $type = new ReturnItemType();
        $form = $this->createForm(ReturnItemTypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);
            $this->addFlash('success', 'admin.return_item_types.add.success');
            return $this->redirectToRoute('admin_return_item_types');
        }

        return $this->render('admin/return_item_types/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_return_item_type')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ReturnItemType $type): Response {
        $form = $this->createForm(ReturnItemTypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);
            $this->addFlash('success', 'admin.return_item_types.edit.success');
            return $this->redirectToRoute('admin_return_item_types');
        }

        return $this->render('admin/return_item_types/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_return_item_type')]
    public function remove(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ReturnItemType $type, ReturnItemRepositoryInterface $itemRepository): Response {
        $count = $itemRepository->countByType($type);

        if($count > 0) {
            $this->addFlash('error', 'admin.return_item_types.remove.error');
            return $this->redirectToRoute('admin_return_item_types');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.return_item_types.remove.confirm',
            'message_parameters' => [
                '%name%' => $type->getDisplayName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($type);
            $this->addFlash('success', 'admin.return_item_types.remove.success');

            return $this->redirectToRoute('admin_return_item_types');
        }

        return $this->render('admin/return_item_types/remove.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }
}