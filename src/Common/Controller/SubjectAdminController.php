<?php

namespace App\Common\Controller;

use App\Common\Entity\Subject;
use App\Common\Form\SubjectType;
use App\Common\Repository\SubjectRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Framework\Controller\AbstractController;
use App\Framework\Repository\PaginationQuery;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/subjects')]
class SubjectAdminController extends AbstractController {

    public function __construct(
        private readonly SubjectRepositoryInterface $repository,
        RefererHelper $redirectHelper
    ) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_subjects')]
    public function index(
        #[MapQueryParameter] int $page = 1
    ): Response {
        $subjects = $this->repository->findPaginated(new PaginationQuery(page: $page));

        return $this->render('admin/subjects/index.html.twig', [
            'subjects' => $subjects
        ]);
    }

    #[Route(path: '/add', name: 'new_subject')]
    public function add(Request $request): Response {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($subject);
            $this->addFlash('success', 'admin.subjects.add.success');
            return $this->redirectToRoute('admin_subjects');
        }

        return $this->render('admin/subjects/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_subject')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Subject $subject, Request $request): Response {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($subject);
            $this->addFlash('success', 'admin.subjects.edit.success');
            return $this->redirectToRoute('admin_subjects');
        }

        return $this->render('admin/subjects/edit.html.twig', [
            'form' => $form->createView(),
            'subject' => $subject
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_subject')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Subject $subject, Request $request, TuitionRepositoryInterface $tuitionRepository): Response {
        $tuitions = $tuitionRepository->findAllBySubjects([$subject]);

        if(count($tuitions) > 0) {
            $this->addFlash('error', 'admin.subjects.remove.error');
            return $this->redirectToRoute('admin_subjects');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.subjects.remove.confirm',
            'message_parameters' => [
                '%name%' => $subject->getName(),
                '%abbreviation%' => $subject->getAbbreviation()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($subject);
            $this->addFlash('success', 'admin.subjects.remove.success');
            return $this->redirectToRoute('admin_subjects');
        }

        return $this->render('admin/subjects/remove.html.twig', [
            'form' => $form->createView(),
            'subject' => $subject
        ]);
    }
}