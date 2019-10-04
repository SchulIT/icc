<?php

namespace App\Controller;

use App\Entity\TeacherTag;
use App\Form\TeacherTagType;
use App\Repository\TeacherTagRepositoryInterface;
use App\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/teachers/tags")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class TeacherTagAdminController extends AbstractController {

    private $repository;

    public function __construct(TeacherTagRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_teacher_tags")
     */
    public function index() {
        return $this->render('admin/teachers/tags/index.html.twig', [
            'tags' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_teacher_tag")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{id}/edit", name="edit_teacher_tag")
     */
    public function edit(TeacherTag $tag, Request $request) {
        $form = $this->createForm(TeacherTagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($tag);

            $this->addFlash('success', 'admin.teachers.edit.success');
            return $this->redirectToRoute('admin_teacher_tags');
        }

        return $this->render('admin/teachers/tags/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_teacher_tag")
     */
    public function remove() {

    }
}