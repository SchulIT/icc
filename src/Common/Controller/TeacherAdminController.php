<?php

namespace App\Common\Controller;

use App\Common\Converter\TeacherStringConverter;
use App\Common\Entity\Teacher;
use App\Common\Form\TeacherType;
use App\Common\Repository\TeacherRepositoryInterface;
use App\Common\View\Filter\SubjectFilter;
use App\Common\View\Filter\TeacherTagFilter;
use App\Framework\Controller\AbstractController;
use App\Framework\Repository\PaginationQuery;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/teachers')]
#[IsGranted('ROLE_ADMIN')]
class TeacherAdminController extends AbstractController {

    public function __construct(private TeacherRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_teachers')]
    public function index(
        TeacherTagFilter $teacherTagFilter,
        SubjectFilter $subjectFilter,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(name: 'tag', filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $teacherTagUuid = null,
        #[MapQueryParameter(name: 'subject', filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $subjectUuid = null,
    ): Response {
        $teacherTagFilterView = $teacherTagFilter->handle($teacherTagUuid);
        $subjectFilterView = $subjectFilter->handle($subjectUuid);

        $teachers = $this->repository->findPaginated(
            new PaginationQuery(page: $page),
            subject: $subjectFilterView->getCurrentSubject(),
            teacherTag: $teacherTagFilterView->getCurrentTag()
        );

        return $this->render('admin/teachers/index.html.twig', [
            'teachers' => $teachers,
            'teacherTagFilter' => $teacherTagFilterView,
            'subjectFilter' => $subjectFilterView,
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
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Teacher $teacher, Request $request): Response {
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
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Teacher $teacher, Request $request, TranslatorInterface $translator, TeacherStringConverter $teacherStringConverter): Response {
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