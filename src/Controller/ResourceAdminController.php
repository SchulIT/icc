<?php

namespace App\Controller;

use App\Entity\ResourceEntity;
use App\Entity\Room;
use App\Form\ResourceType;
use App\Repository\ResourceRepositoryInterface;
use App\Repository\ResourceTypeRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Sorting\ResourceStrategy;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/resources")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ResourceAdminController extends AbstractController {

    private $repository;

    public function __construct(ResourceRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_resources")
     */
    public function index(Sorter $sorter) {
        $resources = $this->repository->findAll();
        $sorter->sort($resources, ResourceStrategy::class);

        return $this->render('admin/resources/index.html.twig', [
            'resources' => $resources
        ]);
    }

    /**
     * @Route("/add", name="add_resource")
     */
    public function add(Request $request, ResourceTypeRepositoryInterface $typeRepository) {
        $resource = new ResourceEntity();

        if($request->query->get('type') === 'room') {
            $resource = new Room();
            $resource->setType($typeRepository->findRoomType());
        }

        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($resource instanceof Room) {
                $resource->ensureAllTagsHaveRoomAssociated();
            }
            $this->repository->persist($resource);
            $this->addFlash('success', 'admin.resources.add.success');

            return $this->redirectToRoute('admin_resources');
        }

        return $this->render('admin/resources/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_resource")
     */
    public function edit(ResourceEntity $room, Request $request) {
        $form = $this->createForm(ResourceType::class, $room);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($room instanceof Room) {
                $room->ensureAllTagsHaveRoomAssociated();
            }
            $this->repository->persist($room);
            $this->addFlash('success', 'admin.resources.edit.success');

            return $this->redirectToRoute('admin_resources');
        }

        return $this->render('admin/resources/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_resource")
     */
    public function remove(ResourceEntity $room, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.resources.remove.confirm',
            'message_parameters' => [
                '%name%' => $room->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($room);
            $this->addFlash('success', 'admin.resources.remove.success');

            return $this->redirectToRoute('admin_resources');
        }

        return $this->render('admin/resources/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}