<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\SubjectAbbreviationStrategy;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/subjects")
 */
class SubjectAdminController extends AbstractController {

    private Sorter $sorter;
    private SubjectRepositoryInterface $repository;

    public function __construct(Sorter $sorter, SubjectRepositoryInterface $subjectRepository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->sorter = $sorter;
        $this->repository = $subjectRepository;
    }

    /**
     * @Route("", name="admin_subjects")
     */
    public function index() {
        $subjects = $this->repository->findAll();
        $this->sorter->sort($subjects, SubjectAbbreviationStrategy::class);

        return $this->render('admin/subjects/index.html.twig', [
            'subjects' => $subjects
        ]);
    }

    /**
     * @Route("/add", name="new_subject")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{uuid}/edit", name="edit_subject")
     */
    public function edit(Subject $subject, Request $request) {
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

    /**
     * @Route("/{uuid}/remove", name="remove_subject")
     */
    public function remove(Subject $subject, Request $request, TuitionRepositoryInterface $tuitionRepository) {
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