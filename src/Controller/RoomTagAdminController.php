<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\RoomTag;
use App\Form\RoomTagType;
use App\Repository\RoomTagRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/resources/tags')]
#[Security("is_granted('ROLE_APPOINTMENTS_ADMIN')")]
class RoomTagAdminController extends AbstractController {

    public function __construct(private RoomTagRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_room_tags')]
    public function index(): Response {
        return $this->render('admin/resources/tags/index.html.twig', [
            'tags' => $this->repository->findAll()
        ]);
    }

    #[Route(path: '/add', name: 'add_room_tag')]
    public function add(Request $request): Response {
        $tag = new RoomTag();
        $form = $this->createForm(RoomTagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);
            $this->addFlash('success', 'admin.resources.tags.add.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/resources/tags/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_room_tag')]
    public function edit(RoomTag $tag, Request $request): Response {
        $form = $this->createForm(RoomTagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);
            $this->addFlash('success', 'admin.resources.tags.add.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/resources/tags/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_room_tag')]
    public function remove(RoomTag $tag, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.resources.tags.remove.confirm',
            'message_parameters' => [
                '%name%' => $tag->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($tag);
            $this->addFlash('success', 'admin.resources.tags.remove.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/resources/tags/remove.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }

}