<?php

namespace App\Controller;

use App\Entity\Display;
use App\Form\DisplayType;
use App\Repository\DisplayRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/displays")
 */
class DisplayAdminController extends AbstractController {

    private $repository;

    public function __construct(DisplayRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_displays")
     */
    public function index() {
        return $this->render('admin/displays/index.html.twig', [
            'displays' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_display")
     */
    public function add(Request $request) {
        $display = new Display();
        $form = $this->createForm(DisplayType::class, $display);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($display);
            $this->addFlash('success', 'admin.displays.add.success');

            return $this->redirectToRoute('admin_displays');
        }

        return $this->render('admin/displays/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_display")
     */
    public function edit(Display $display, Request $request) {
        $form = $this->createForm(DisplayType::class, $display);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($display);
            $this->addFlash('success', 'admin.displays.edit.success');

            return $this->redirectToRoute('admin_displays');
        }

        return $this->render('admin/displays/edit.html.twig', [
            'form' => $form->createView(),
            'display' => $display
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_display")
     */
    public function remove(Display $display, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.displays.remove.confirm',
            'message_parameters' => [
                '%name%' => $display->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($display);
            $this->addFlash('success', 'admin.displays.remove.success');

            return $this->redirectToRoute('admin_displays');
        }

        return $this->render('admin/displays/remove.html.twig', [
            'form' => $form->createView(),
            'display' => $display
        ]);
    }
}
