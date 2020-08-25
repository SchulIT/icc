<?php

namespace App\Controller;

use App\Entity\RoomTag;
use App\Form\RoomTagType;
use App\Repository\RoomTagRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/rooms/tags")
 * @Security("is_granted('ROLE_APPOINTMENTS_ADMIN')")
 */
class RoomTagAdminController extends AbstractController {

    private $repository;

    public function __construct(RoomTagRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_room_tags")
     */
    public function index() {
        return $this->render('admin/rooms/tags/index.html.twig', [
            'tags' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_room_tag")
     */
    public function add(Request $request) {
        $tag = new RoomTag();
        $form = $this->createForm(RoomTagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);
            $this->addFlash('success', 'admin.rooms.tags.add.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/rooms/tags/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_room_tag")
     */
    public function edit(RoomTag $tag, Request $request) {
        $form = $this->createForm(RoomTagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);
            $this->addFlash('success', 'admin.rooms.tags.add.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/rooms/tags/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_room_tag")
     */
    public function remove(RoomTag $tag, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.rooms.tags.remove.confirm',
            'message_parameters' => [
                '%name%' => $tag->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($tag);
            $this->addFlash('success', 'admin.rooms.tags.remove.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/rooms/tags/remove.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }

}