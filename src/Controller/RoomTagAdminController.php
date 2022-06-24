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
 * @Route("/admin/resources/tags")
 * @Security("is_granted('ROLE_APPOINTMENTS_ADMIN')")
 */
class RoomTagAdminController extends AbstractController {

    private RoomTagRepositoryInterface $repository;

    public function __construct(RoomTagRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_room_tags")
     */
    public function index() {
        return $this->render('admin/resources/tags/index.html.twig', [
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
            $this->addFlash('success', 'admin.resources.tags.add.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/resources/tags/add.html.twig', [
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
            $this->addFlash('success', 'admin.resources.tags.add.success');

            return $this->redirectToRoute('admin_room_tags');
        }

        return $this->render('admin/resources/tags/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_room_tag")
     */
    public function remove(RoomTag $tag, Request $request) {
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