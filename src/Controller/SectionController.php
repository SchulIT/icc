<?php

namespace App\Controller;

use App\Entity\Section;
use App\Form\SectionType;
use App\Repository\SectionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/section")
 */
class SectionController extends AbstractController {

    private SectionRepositoryInterface $repository;

    public function __construct(SectionRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_sections")
     */
    public function index() {
        return $this->render('admin/sections/index.html.twig', [
            'sections' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_section")
     */
    public function add(Request $request) {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($section);
            $this->addFlash('success', 'admin.sections.add.success');

            return $this->redirectToRoute('admin_sections');
        }

        return $this->render('admin/sections/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_section")
     */
    public function edit(Section $section, Request $request) {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($section);
            $this->addFlash('success', 'admin.sections.add.success');

            return $this->redirectToRoute('admin_sections');
        }

        return $this->render('admin/sections/edit.html.twig', [
            'section' => $section,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_section")
     */
    public function remove(Section $section, Request $request, TimetableLessonRepositoryInterface $lessonRepository) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.sections.remove.confirm',
            'message_parameters' => [
                '%name%' => $section->getDisplayName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $lessonRepository->removeRange($section->getStart(), $section->getEnd());
            $this->repository->remove($section);
            $this->addFlash('success', 'admin.sections.remove.success');

            return $this->redirectToRoute('admin_sections');
        }

        return $this->render('admin/sections/remove.html.twig', [
            'section' => $section,
            'form' => $form->createView()
        ]);
    }
}