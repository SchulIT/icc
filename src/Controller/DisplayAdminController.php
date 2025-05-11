<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Display;
use App\Form\DisplayType;
use App\Repository\DisplayRepositoryInterface;
use App\Settings\DisplaySettings;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/displays')]
class DisplayAdminController extends AbstractController {

    public function __construct(private DisplayRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_displays')]
    public function index(Request $request, DisplaySettings $settings): Response {
        $form = $this->createFormBuilder()
            ->add('allowedIps', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'label.ip_addresses.label',
                'help' => 'label.ip_addresses.help',
                'data' => $settings->getAllowedIpAddresses()
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $settings->setAllowedIpAddresses($form->get('allowedIps')->getData());
            $this->addFlash('success', 'admin.displays.settings.success');

            return $this->redirectToRoute('admin_displays');
        }

        return $this->render('admin/displays/index.html.twig', [
            'displays' => $this->repository->findAll(),
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/add', name: 'add_display')]
    public function add(Request $request): Response {
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

    #[Route(path: '/{uuid}/edit', name: 'edit_display')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Display $display, Request $request): Response {
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

    #[Route(path: '/{uuid}/remove', name: 'remove_display')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Display $display, Request $request): Response {
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
