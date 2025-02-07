<?php

namespace App\Controller;

use App\Entity\TeacherTag;
use App\Form\TeacherTagType;
use App\Repository\TeacherTagRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/teachers/tags')]
#[IsGranted('ROLE_ADMIN')]
class TeacherTagAdminController extends AbstractController {

    public function __construct(private TeacherTagRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_teacher_tags')]
    public function index(): Response {
        return $this->render('admin/teachers/tags/index.html.twig', [
            'tags' => $this->repository->findAll()
        ]);
    }

    #[Route(path: '/add', name: 'add_teacher_tag')]
    public function add(Request $request): Response {
        $tag = new TeacherTag();
        $form = $this->createForm(TeacherTagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);

            $this->addFlash('success', 'admin.teachers.tags.add.success');
            return $this->redirectToRoute('admin_teacher_tags');
        }

        return $this->render('admin/teachers/tags/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_teacher_tag')]
    public function edit(TeacherTag $tag, Request $request): Response {
        $form = $this->createForm(TeacherTagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);

            $this->addFlash('success', 'admin.teachers.edit.success');
            return $this->redirectToRoute('admin_teacher_tags');
        }

        return $this->render('admin/teachers/tags/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_teacher_tag')]
    public function remove(TeacherTag $tag, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.teachers.tags.remove.confirm', [
                '%name%' => $tag->getName()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($tag);

            $this->addFlash('success', 'admin.teachers.tags.remove.success');

            return $this->redirectToRoute('admin_teacher_tags');
        }

        return $this->render('admin/teachers/tags/remove.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag
        ]);
    }
}