<?php

namespace App\Controller;

use App\Converter\TeacherStringConverter;
use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Repository\TeacherRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TeacherStrategy;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/teachers')]
#[IsGranted('ROLE_ADMIN')]
class TeacherAdminController extends AbstractController {

    public function __construct(private Sorter $sorter, private TeacherRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_teachers')]
    public function index(): Response {
        $teachers = $this->repository->findAll();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        return $this->render('admin/teachers/index.html.twig', [
            'teachers' => $teachers
        ]);
    }

    #[Route(path: '/add', name: 'add_teacher')]
    public function add(Request $request): Response {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($teacher);

            $this->addFlash('success', 'admin.teachers.add.success');
            return $this->redirectToRoute('admin_teachers');
        }

        return $this->render('admin/teachers/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_teacher')]
    public function edit(Teacher $teacher, Request $request): Response {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($teacher);

            $this->addFlash('success', 'admin.teachers.edit.success');
            return $this->redirectToRoute('admin_teachers');
        }

        return $this->render('admin/teachers/edit.html.twig', [
            'form' => $form->createView(),
            'teacher' => $teacher
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_teacher')]
    public function remove(Teacher $teacher, Request $request, TranslatorInterface $translator, TeacherStringConverter $teacherStringConverter): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.teachers.remove.confirm', [
                '%name%' => $teacherStringConverter->convert($teacher),
                '%acronym%' => $teacher->getAcronym()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($teacher);

            $this->addFlash('success', 'admin.teachers.remove.success');

            return $this->redirectToRoute('admin_teachers');
        }

        return $this->render('admin/teachers/remove.html.twig', [
            'form' => $form->createView(),
            'teacher' => $teacher
        ]);
    }
}