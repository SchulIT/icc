<?php

namespace App\Controller;

use App\Entity\ResourceType;
use App\Form\ResourceTypeType;
use App\Form\RoomTagType;
use App\Repository\ResourceTypeRepositoryInterface;
use App\Security\Voter\ResourceTypeVoter;
use App\Sorting\ResourceTypeStrategy;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/resources/types")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ResourceTypeAdminController extends AbstractController {

    private ResourceTypeRepositoryInterface $repository;

    public function __construct(ResourceTypeRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_resource_types")
     */
    public function index(Sorter $sorter) {
        $types = $this->repository->findAll();
        $sorter->sort($types, ResourceTypeStrategy::class);

        return $this->render('admin/resources/types/index.html.twig', [
            'types' => $types
        ]);
    }

    /**
     * @Route("/add", name="add_resource_type")
     */
    public function add(Request $request) {
        $this->denyAccessUnlessGranted(ResourceTypeVoter::New);

        $tag = new ResourceType();
        $form = $this->createForm(ResourceTypeType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);
            $this->addFlash('success', 'admin.resources.types.add.success');

            return $this->redirectToRoute('admin_resource_types');
        }

        return $this->render('admin/resources/types/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_resource_type")
     */
    public function edit(ResourceType $type, Request $request) {
        $this->denyAccessUnlessGranted(ResourceTypeVoter::Edit, $type);

        $form = $this->createForm(ResourceTypeType::class, $type);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);
            $this->addFlash('success', 'admin.resources.types.add.success');

            return $this->redirectToRoute('admin_resource_types');
        }

        return $this->render('admin/resources/types/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $type
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_resource_type")
     */
    public function remove(ResourceType $type, Request $request) {
        $this->denyAccessUnlessGranted(ResourceTypeVoter::Remove, $type);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.resources.types.remove.confirm',
            'message_parameters' => [
                '%name%' => $type->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($type);
            $this->addFlash('success', 'admin.resources.types.remove.success');

            return $this->redirectToRoute('admin_resource_types');
        }

        return $this->render('admin/resources/types/remove.html.twig', [
            'form' => $form->createView(),
            'tag' => $type
        ]);
    }

}