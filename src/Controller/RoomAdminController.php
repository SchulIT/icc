<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/rooms")
 * @Security("is_granted('ROLE_APPOINTMENTS_ADMIN')")
 */
class RoomAdminController extends AbstractController {

    private $repository;

    public function __construct(RoomRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_rooms")
     */
    public function index() {
        return $this->render('admin/rooms/index.html.twig', [
            'rooms' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_room")
     */
    public function add(Request $request) {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $room->ensureAllTagsHaveRoomAssociated();
            $this->repository->persist($room);
            $this->addFlash('success', 'admin.rooms.add.success');

            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_room")
     */
    public function edit(Room $room, Request $request) {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $room->ensureAllTagsHaveRoomAssociated();
            $this->repository->persist($room);
            $this->addFlash('success', 'admin.rooms.edit.success');

            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_room")
     */
    public function remove(Room $room, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.rooms.remove.confirm',
            'message_parameters' => [
                '%name%' => $room->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($room);
            $this->addFlash('success', 'admin.rooms.remove.success');

            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}