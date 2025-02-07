<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/resources')]
#[IsGranted('ROLE_ADMIN')]
class ResourceAdminController extends AbstractController {

    public function __construct(private ResourceRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_resources')]
    public function index(Sorter $sorter): Response {
        $resources = $this->repository->findAll();
        $sorter->sort($resources, ResourceStrategy::class);

        return $this->render('admin/resources/index.html.twig', [
            'resources' => $resources
        ]);
    }

    #[Route(path: '/add', name: 'add_resource')]
    public function add(Request $request, ResourceTypeRepositoryInterface $typeRepository): Response {
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

    #[Route(path: '/{uuid}/edit', name: 'edit_resource')]
    public function edit(ResourceEntity $room, Request $request): Response {
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

    #[Route(path: '/{uuid}/remove', name: 'remove_resource')]
    public function remove(ResourceEntity $room, Request $request): Response {
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